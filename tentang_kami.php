<?php
// File: tentang_kami.php
// Halaman tentang toko jenang

// Include koneksi database
require_once 'config/koneksi.php';
require_once 'config/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Jenang Kudus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Include navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <!-- Hero Section -->
        <div class="about-hero p-4 mb-5 bg-light rounded">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-3">Tentang Jenang Kudus</h1>
                    <p class="lead">Melestarikan tradisi kuliner nusantara melalui jenang berkualitas yang dibuat dengan resep turun temurun.</p>
                </div>
                <div class="col-md-6">
                    <img src="assets/img/about-hero.jpg" alt="Toko Jenang Kudus" class="img-fluid rounded">
                </div>
            </div>
        </div>
        
        <!-- Sejarah -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-4">Sejarah Toko Jenang Kudus</h2>
                        <div class="row">
                            <div class="col-md-4 mb-4 mb-md-0">
                                <img src="assets/img/history.jpg" alt="Sejarah Jenang Kudus" class="img-fluid rounded">
                            </div>
                            <div class="col-md-8">
                                <p>Toko Jenang Kudus berdiri sejak tahun 1980, diawali dengan sebuah usaha rumahan yang dirintis oleh Bapak H. Abdul Karim dan Ibu Hj. Siti Fatimah. Dengan berbekal resep warisan keluarga, mereka mulai membuat jenang secara tradisional di dapur rumah mereka di kota Kudus.</p>
                                
                                <p>Seiring berjalannya waktu, jenang buatan keluarga Abdul Karim semakin dikenal dan diminati oleh masyarakat sekitar. Pada tahun 1990, usaha rumahan ini berkembang menjadi sebuah toko kecil di pusat kota Kudus. Konsistensi kualitas dan cita rasa yang khas membuat jenang produksi mereka semakin populer dan menjadi buah tangan khas Kudus.</p>
                                
                                <p>Kini, di bawah kepemimpinan generasi kedua, Toko Jenang Kudus telah berkembang pesat dengan berbagai varian produk jenang tradisional yang tetap mempertahankan cita rasa asli namun juga berinovasi sesuai perkembangan zaman. Komitmen kami tetap sama: melestarikan warisan kuliner Indonesia dengan kualitas terbaik.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Visi & Misi -->
        <div class="row mb-5">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="mb-3">Visi</h3>
                        <p>Menjadi perusahaan jenang terkemuka di Indonesia yang dikenal karena kualitas, inovasi, dan kontribusinya dalam melestarikan makanan tradisional Indonesia.</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-transparent">Menjadi produsen jenang terbaik di Indonesia</li>
                            <li class="list-group-item bg-transparent">Memperkenalkan jenang sebagai kuliner tradisional Indonesia ke mancanegara</li>
                            <li class="list-group-item bg-transparent">Menjadi perusahaan yang berkontribusi positif bagi masyarakat dan lingkungan</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="mb-3">Misi</h3>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-transparent">Memproduksi jenang dengan bahan-bahan berkualitas tanpa bahan pengawet berbahaya</li>
                            <li class="list-group-item bg-transparent">Melestarikan resep tradisional jenang Kudus sekaligus berinovasi dengan varian baru</li>
                            <li class="list-group-item bg-transparent">Meningkatkan kesejahteraan petani lokal dengan membeli bahan baku dari mereka</li>
                            <li class="list-group-item bg-transparent">Menjalankan bisnis dengan memperhatikan keberlanjutan lingkungan</li>
                            <li class="list-group-item bg-transparent">Memberikan pelayanan terbaik kepada pelanggan</li>
                            <li class="list-group-item bg-transparent">Memberdayakan masyarakat sekitar dengan membuka lapangan kerja</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Proses Pembuatan -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-4">Proses Pembuatan Jenang</h2>
                        <div class="row">
                            <div class="col-md-7 mb-4 mb-md-0">
                                <p>Jenang Kudus kami dibuat dengan metode tradisional yang telah diwariskan selama beberapa generasi, dengan tetap memperhatikan standar kebersihan dan keamanan pangan modern.</p>
                                
                                <ol>
                                    <li class="mb-2"><strong>Pemilihan Bahan</strong> - Kami hanya menggunakan bahan-bahan terbaik dan segar untuk jenang kami, termasuk tepung beras, gula merah, santan, dan rempah-rempah pilihan.</li>
                                    <li class="mb-2"><strong>Pengolahan Bahan</strong> - Bahan-bahan diolah secara terpisah untuk memastikan kualitas dan rasa yang optimal.</li>
                                    <li class="mb-2"><strong>Pencampuran dan Pemasakan</strong> - Semua bahan dicampur dalam wajan besar dan dimasak dengan api kecil selama berjam-jam, sambil terus diaduk agar tidak gosong.</li>
                                    <li class="mb-2"><strong>Pengadukan</strong> - Proses pengadukan dilakukan secara terus-menerus hingga adonan mencapai tekstur yang kental dan kalis.</li>
                                    <li class="mb-2"><strong>Pendinginan dan Pemotongan</strong> - Setelah matang, jenang didinginkan sebelum dipotong dan dikemas.</li>
                                    <li><strong>Pengemasan</strong> - Jenang dikemas dengan rapi dan higienis untuk menjaga kualitas dan masa simpan.</li>
                                </ol>
                            </div>
                            <div class="col-md-5">
                                <img src="assets/img/production.jpg" alt="Proses Pembuatan Jenang" class="img-fluid rounded">
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <img src="assets/img/ingredients.jpg" alt="Bahan Jenang" class="img-fluid rounded">
                                    </div>
                                    <div class="col-6">
                                        <img src="assets/img/packaging.jpg" alt="Pengemasan Jenang" class="img-fluid rounded">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tim Kami -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <h2 class="mb-4">Tim Kami</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="assets/img/team-1.jpg" class="card-img-top" alt="CEO Jenang Kudus">
                            <div class="card-body text-center">
                                <h5 class="card-title">H. Ahmad Karim</h5>
                                <p class="card-text text-muted">CEO & Founder</p>
                                <p class="card-text">Putra dari pendiri Toko Jenang Kudus yang melanjutkan usaha keluarga dengan inovasi dan perluasan pasar.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="assets/img/team-2.jpg" class="card-img-top" alt="Production Manager">
                            <div class="card-body text-center">
                                <h5 class="card-title">Ibu Siti Aisyah</h5>
                                <p class="card-text text-muted">Production Manager</p>
                                <p class="card-text">Ahli dalam pengolahan jenang dengan pengalaman lebih dari 25 tahun, menjaga kualitas dan cita rasa asli.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="assets/img/team-3.jpg" class="card-img-top" alt="Marketing Manager">
                            <div class="card-body text-center">
                                <h5 class="card-title">Dian Permata</h5>
                                <p class="card-text text-muted">Marketing Manager</p>
                                <p class="card-text">Bertanggung jawab atas strategi pemasaran dan perluasan pasar ke seluruh Indonesia melalui platform digital.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Testimonial -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <h2 class="mb-4">Apa Kata Pelanggan Kami</h2>
                <div class="row">
                    <?php
                    // Get some testimonials
                    $query = "SELECT t.*, c.nama FROM testimonial t 
                             JOIN customer c ON t.id_customer = c.id_customer 
                             WHERE t.status = 'active'
                             ORDER BY t.tanggal DESC LIMIT 3";
                    $result = mysqli_query($koneksi, $query);
                    
                    while ($testimonial = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="rating mb-2">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $testimonial['rating']) {
                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                        } else {
                                            echo '<i class="bi bi-star text-warning"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <p class="card-text">"<?php echo $testimonial['testimonial']; ?>"</p>
                                <p class="card-text text-end mb-0">
                                    <strong><?php echo $testimonial['nama']; ?></strong><br>
                                    <small class="text-muted"><?php echo date('d F Y', strtotime($testimonial['tanggal'])); ?></small>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Kontak & Maps -->
        <div class="row mb-5">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="mb-3">Kontak Kami</h3>
                        <p class="mb-4">Jika Anda memiliki pertanyaan atau ingin informasi lebih lanjut, jangan ragu untuk menghubungi kami:</p>
                        <div class="d-flex mb-3">
                            <div class="me-3 text-primary">
                                <i class="bi bi-geo-alt-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Alamat</h5>
                                <p class="mb-0">Jl. Sunan Kudus No. 123, Kudus, Jawa Tengah 59316, Indonesia</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="me-3 text-primary">
                                <i class="bi bi-telephone-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Telepon</h5>
                                <p class="mb-0">(0291) 123456</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="me-3 text-primary">
                                <i class="bi bi-envelope-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Email</h5>
                                <p class="mb-0">info@jenangkudus.com</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <i class="bi bi-clock-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Jam Operasional</h5>
                                <p class="mb-0">Senin - Sabtu: 08.00 - 17.00<br>Minggu: 09.00 - 15.00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="mb-3">Lokasi Kami</h3>
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31695.30456212787!2d110.81274022418619!3d-6.805488194478569!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e70c4b7d3a1ea81%3A0x2a3fe7dd6ee91eba!2sKudus%2C%20Kudus%20Regency%2C%20Central%20Java!5e0!3m2!1sen!2sid!4v1684841841379!5m2!1sen!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="mt-3">
                            <p class="mb-0">Anda juga dapat mengunjungi toko kami langsung. Kami berlokasi strategis di pusat kota Kudus, dekat dengan Menara Kudus.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>