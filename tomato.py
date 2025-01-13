import matplotlib.pyplot as plt
import psutil
import time
import subprocess
import os
from datetime import datetime, timedelta

# Paths for data files
data_paths = {
    "hourly": "/var/www/html/jocarsa-tomato/carga_hourly.txt",
    "minute": "/var/www/html/jocarsa-tomato/carga_60min.txt",
    "second": "/var/www/html/jocarsa-tomato/carga_300sec.txt",
}

# Paths for plot folders
plot_folders = {
    "hourly": "/var/www/html/jocarsa-tomato/img/hourly",
    "minute": "/var/www/html/jocarsa-tomato/img/minute",
    "second": "/var/www/html/jocarsa-tomato/img/second",
}

# Create folders if they don't exist
for folder in plot_folders.values():
    os.makedirs(folder, exist_ok=True)

# Function to trim data based on a time window
def trim_data(data, time_window_seconds):
    now = datetime.now()
    return [entry for entry in data if (now - entry[0]).total_seconds() <= time_window_seconds]

# Load existing data
def load_data(file_path):
    try:
        with open(file_path, 'r') as f:
            return [
                (datetime.fromisoformat(row[0]), *map(float, row[1:]))
                for row in (line.strip().split(',') for line in f if line.strip())
            ]
    except FileNotFoundError:
        return []

# Save data to file
def save_data(file_path, data):
    with open(file_path, 'w') as f:
        for row in data:
            f.write(','.join(map(str, [row[0].isoformat()] + list(row[1:]))) + '\n')

# Measure system metrics
def measure_metrics():
    carga_cpu = psutil.cpu_percent(interval=1)
    carga_ram = psutil.virtual_memory().percent
    uso_disco = psutil.disk_usage('/').percent
    data_inicio = psutil.net_io_counters()
    time.sleep(1)
    data_final = psutil.net_io_counters()
    descarga_mbps = (data_final.bytes_recv - data_inicio.bytes_recv) / (1024 * 1024)
    subida_mbps = (data_final.bytes_sent - data_inicio.bytes_sent) / (1024 * 1024)
    num_conexiones = len(psutil.net_connections())
    temperaturas = list(obtener_temperaturas())
    temperatura_promedio = sum(temperaturas) / len(temperaturas) if temperaturas else 0
    return (
        datetime.now(),
        carga_cpu,
        carga_ram,
        uso_disco,
        descarga_mbps,
        subida_mbps,
        temperatura_promedio,
        num_conexiones,
    )

# Function to obtain CPU temperatures (requires lm-sensors)
def obtener_temperaturas():
    # try:
    #     sensores = subprocess.check_output(['sensors'], encoding='utf-8')
    #     for linea in sensores.splitlines():
    #         if 'Core' in linea:
    #             yield float(linea.split()[1].strip('+').strip('°C'))
    # except Exception as e:
    #     print(f"Error al obtener temperaturas: {e}")
    #     return []
    return []

# Load current data
data_buffers = {key: load_data(path) for key, path in data_paths.items()}

# Measure metrics
new_entry = measure_metrics()

# Update data buffers
data_buffers["hourly"].append(new_entry)
data_buffers["minute"].append(new_entry)
data_buffers["second"].append(new_entry)

# Trim data
data_buffers["hourly"] = trim_data(data_buffers["hourly"], 3600)  # Last 1 hour
data_buffers["minute"] = trim_data(data_buffers["minute"], 3600)  # Last 60 minutes
data_buffers["second"] = trim_data(data_buffers["second"], 300)  # Last 300 seconds

# Save updated data
for key, path in data_paths.items():
    save_data(path, data_buffers[key])

# Function to generate plots
def generate_plot(data, index, title, ylabel, save_path, ylim=None):
    timestamps = [row[0] for row in data]
    values = [row[index] for row in data]
    plt.figure(figsize=(10, 6))
    plt.plot(timestamps, values, label=title, marker='o')
    plt.grid(True)
    if ylim:
        plt.ylim(ylim)
    plt.title(title)
    plt.xlabel('Tiempo')
    plt.ylabel(ylabel)
    plt.legend()
    plt.savefig(save_path)
    plt.close()

# Plot settings
plot_configs = [
    (1, 'Uso de CPU', 'Porcentaje de Uso', (0, 100)),
    (2, 'Uso de RAM', 'Porcentaje de Uso', (0, 100)),
    (3, 'Uso de Disco', 'Porcentaje de Uso', (0, 100)),
    (4, 'Descarga', 'Mbps', None),
    (5, 'Subida', 'Mbps', None),
    (6, 'Temperatura', 'Temperatura (°C)', None),
    (7, 'Conexiones Activas', 'Conexiones', None),
]

# Generate plots for each time window
for time_window, folder in plot_folders.items():
    for index, title, ylabel, ylim in plot_configs:
        generate_plot(
            data_buffers[time_window],
            index,
            f'{title} ({time_window.capitalize()})',
            ylabel,
            os.path.join(folder, f'{title.lower().replace(" ", "_")}_{time_window}.jpg'),
            ylim,
        )

print("Métricas actualizadas y gráficas generadas correctamente.")

