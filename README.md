# GreedyScheduler (Ders Programı Oluşturma Projesi)

## Genel Bakış

Bu yazılım geliştirme projesi, ders programlarını optimize etmek ve çakışmaları en aza indirmek amacıyla geliştirilen bir çözümü içermektedir. Proje, Greedy graph coloring algoritmasını kullanarak ders programlarını düzenleyen bir web tabanlı uygulama üzerinde odaklanmaktadır.

## Kullanılan Teknolojiler ve Anahtar Kavramlar

- **Veri Tabanı:** MySQL veri tabanında "Dersler", "Hocalar", ve "Hoca_ders" adlı üç tablo kullanılarak temel veri yönetimi sağlanmıştır.
- **Web Teknolojileri:** XAMPP ve Apache kullanılarak yerel bir sunucu oluşturulmuş, PHP ile veri çekme işlemi ve HTML ile kullanıcı arayüzü tasarlanmıştır.
- **Algoritma:** Greedy graph coloring algoritması, ders programlarını renklendirerek çakışmaları önlemek için kullanılmıştır.
- **Python ve Grafik Kütüphaneleri:** Python dilinde yazılmış bir dosya, networkx ve matplot kütüphaneleri kullanılarak ders programını görsel olarak temsil etmektedir.

## Geliştirme Süreci

1. **Veri Tabanı Oluşturma:** MySQL üzerinde "Dersler", "Hocalar", ve "Hoca_ders" tabloları başarıyla oluşturuldu.
2. **Web Tabanlı Uygulama:** XAMPP ve PHP kullanılarak yerel bir sunucu oluşturuldu. Veri çekme işlemi ve temel HTML arayüzü oluşturuldu.
3. **Algoritmanın Geliştirilmesi:** Greedy graph coloring algoritması kullanılarak ders programlarını renklendirme algoritması geliştirildi.
4. **Veri Düzenleme Sayfası:** JavaScript ve modaller kullanılarak kullanıcı dostu bir veri düzenleme sayfası oluşturuldu.
5. **PDF Çıktısı Alma:** Ders programının onaylanması sonrasında, program PDF dosyasına çevrildi ve kullanıcıya kaydetme imkanı sunuldu.
6. **Grafik Temsil:** Python dilinde yazılan bir dosya ile oluşturulan algoritma, görsel grafik figürler aracılığıyla temsil edildi.

## Başlarken

1. Projeyi klonlayın veya indirin.
2. MySQL veri tabanını oluşturun ve temel verileri ekleyin.
3. XAMPP ve Apache'i kullanarak yerel bir sunucu oluşturun.
4. Projenin ana dizinindeki `index.php` dosyasını çalıştırarak web uygulamasını başlatın.

## Proje Yapısı

- **Veri Tabanı:** MySQL tabloları ve temel veriler içerir.
- **Web Uygulaması:** XAMPP, PHP ve HTML kullanılarak oluşturulmuş, ders programlarını optimize eden uygulamayı içerir.
- **Grafik Temsil:** Python dilinde yazılan ve networkx ve matplot kütüphaneleri kullanılan dosyayı içerir.

