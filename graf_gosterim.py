import random
import matplotlib.pyplot as plt
import pymysql.cursors
import networkx as nx
# Önce bağlantıyı oluşturun
connection = pymysql.connect(
    host='localhost',
    user='root',
    password='root',
    db='proje',
    charset='utf8mb4',
    cursorclass=pymysql.cursors.DictCursor
)

try:
    with connection.cursor() as cursor:
        # Dersleri çekme sorgusu
        sql = """
        SELECT d.ders_id, d.ders_adi, d.ders_sinif, h.hoca_adi, d.gun, d.saat
        FROM dersler d
        JOIN hoca_ders hd ON d.ders_id = hd.ders_id
        JOIN hocalar h ON hd.hoca_id = h.hoca_id
        """
        cursor.execute(sql)
        result = cursor.fetchall()
finally:
    connection.close()


# Olası gün-saat kombinasyonları listesi
days = ["Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma"]
hours = ["10:00", "11:00", "12:00", "13:00", "14:00", "15:00"]
day_hours = [{"day": day, "hour": hour} for day in days for hour in hours]

# Grafik ve çakışma listeleri
graph = {}
coloring = {}
used_colors = []

# Veritabanından dersleri çekme ve işleme
for row in result:
    course = {
        "ders_id": row["ders_id"],
        "ders_adi": row["ders_adi"],
        "ders_sinif": row["ders_sinif"],
        "hoca_adi": row["hoca_adi"],
        "conflicts": [],
        "day_hour": None,
        "color": None,
    }

    # Dersi grafa ekle
    graph[row["ders_adi"]] = course

    # Ders çakışmalarını kontrol et
    for other_course_id, other_course in graph.items():
        if other_course_id == row["ders_adi"]:
            continue  # Aynı dersle çakışmayı kontrol etme

        if (
            course["ders_sinif"] == other_course["ders_sinif"]
            or course["hoca_adi"] == other_course["hoca_adi"]
        ):
            # Çakışan dersi çakışmalar listesine ekle
            graph[row["ders_adi"]]["conflicts"].append(other_course_id)
            graph[other_course_id]["conflicts"].append(row["ders_adi"])

    # Rastgele bir gün-saat kombinasyonu ata
    random_day_hour = random.choice(day_hours)



# Greedy algoritması
for course_id, course in graph.items():
    neighbors = course["conflicts"]

    for color in used_colors:
        conflict = False
        for neighbor_id in neighbors:
            if (
                neighbor_id in coloring
                and coloring[neighbor_id]["color"] == color
            ):
                conflict = True
                break
        if not conflict:
            graph[course_id]["color"] = color
            break

    if graph[course_id]["color"] is None:
        new_color = len(used_colors)
        used_colors.append(new_color)
        graph[course_id]["color"] = new_color

    coloring[course_id] = {
        "course": course,
        "color": graph[course_id]["color"],
    }

# Grafiksel gösterim için Matplotlib kullanımı
G = nx.Graph()

for course_id, data in coloring.items():
    day_hour = data["course"]["day_hour"]
    color = data["color"]
    G.add_node(course_id, label=f"{data['course']['ders_adi']}\n{data['course']['hoca_adi']}", color=color)

for course_id, data in coloring.items():
    neighbors = data["course"]["conflicts"]
    for neighbor_id in neighbors:
        G.add_edge(course_id, neighbor_id)
for course_id, data in coloring.items():
    print(f"Ders: {data['course']['ders_adi']}, Renk: {data['color']}")
    conflicts = data["course"]["conflicts"]
    if conflicts:
        print(f"Çakışan Dersler: {', '.join(graph[conflict]['ders_adi'] for conflict in conflicts)}")
    print()

pos = nx.shell_layout(G)
node_colors = [data["color"] for _, data in coloring.items()]

# Grafik çizimini düzenle
labels = nx.get_node_attributes(G, 'label')
nx.draw_networkx_nodes(G, pos, node_color=node_colors)
nx.draw_networkx_edges(G, pos)
nx.draw_networkx_labels(G, pos, labels, font_size=8)  # with_labels argümanı burada kullanılır
plt.show()
