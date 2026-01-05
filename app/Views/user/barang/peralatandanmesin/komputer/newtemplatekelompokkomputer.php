<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Improved Computer Asset Table Layout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #2c5282;
            --accent-color: #3182ce;
            --success-color: #38a169;
            --warning-color: #d69e2e;
            --danger-color: #e53e3e;
            --border-color: #e2e8f0;
            --bg-subtle: #f7fafc;
            --text-primary: #2d3748;
            --text-secondary: #4a5568;
            --shadow-subtle: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --border-radius: 8px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background-color: var(--bg-subtle);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-card);
        }

        .category-nav {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .category-item {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s ease;
            box-shadow: var(--shadow-subtle);
        }

        .category-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .category-item.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* SOLUTION 1: Card-based Layout */
        .asset-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .asset-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .asset-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card);
        }

        .card-header {
            background: var(--bg-subtle);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1rem;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .asset-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 0.875rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .qr-section {
            display: flex;
            justify-content: center;
            padding: 1rem;
            background: var(--bg-subtle);
            border-radius: var(--border-radius);
            margin: 1rem 0;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .qr-code:hover {
            transform: scale(1.05);
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            background: var(--bg-subtle);
            border-top: 1px solid var(--border-color);
        }

        /* SOLUTION 2: Responsive Table with Collapsible Columns */
        .responsive-table-wrapper {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
        }

        .table-controls {
            padding: 1rem 1.5rem;
            background: var(--bg-subtle);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .column-toggle {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .column-btn {
            padding: 0.25rem 0.75rem;
            border: 1px solid var(--border-color);
            background: white;
            border-radius: 20px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .column-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .responsive-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .responsive-table th {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }

        .responsive-table td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .responsive-table tr:hover {
            background: var(--bg-subtle);
        }

        /* SOLUTION 3: Master-Detail View */
        .master-detail-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            height: 600px;
        }

        .master-list {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
        }

        .list-header {
            padding: 1rem 1.5rem;
            background: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        .list-body {
            height: calc(600px - 60px);
            overflow-y: auto;
        }

        .list-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .list-item:hover {
            background: var(--bg-subtle);
        }

        .list-item.active {
            background: var(--accent-color);
            color: white;
        }

        .item-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .item-subtitle {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .detail-panel {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .detail-header {
            padding: 1rem 1.5rem;
            background: var(--bg-subtle);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
        }

        .detail-content {
            padding: 1.5rem;
            height: calc(600px - 120px);
            overflow-y: auto;
        }

        .detail-grid {
            display: grid;
            gap: 1rem;
        }

        /* Utility Classes */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-primary { background: var(--primary-color); color: white; }
        .badge-success { background: var(--success-color); color: white; }
        .badge-warning { background: var(--warning-color); color: white; }
        .badge-danger { background: var(--danger-color); color: white; }
        .badge-light { background: var(--border-color); color: var(--text-primary); }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary { background: var(--primary-color); color: white; }
        .btn-success { background: var(--success-color); color: white; }
        .btn-warning { background: var(--warning-color); color: white; }
        .btn-danger { background: var(--danger-color); color: white; }
        .btn-outline { background: white; border: 1px solid var(--border-color); color: var(--text-primary); }

        .btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
        }

        .text-currency {
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-weight: 600;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .master-detail-layout {
                grid-template-columns: 1fr;
                height: auto;
            }

            .asset-cards {
                grid-template-columns: 1fr;
            }

            .category-nav {
                grid-template-columns: 1fr;
            }

            .table-controls {
                flex-direction: column;
                align-items: stretch;
            }
        }

        /* Tab System */
        .tab-nav {
            display: flex;
            background: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            border: 1px solid var(--border-color);
            border-bottom: none;
            overflow: hidden;
        }

        .tab-btn {
            flex: 1;
            padding: 1rem;
            border: none;
            background: var(--bg-subtle);
            cursor: pointer;
            font-weight: 500;
            border-right: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }

        .tab-btn:last-child {
            border-right: none;
        }

        .tab-btn.active {
            background: white;
            color: var(--primary-color);
        }

        .tab-content {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            min-height: 500px;
        }

        .tab-pane {
            display: none;
            padding: 2rem;
        }

        .tab-pane.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 style="margin: 0; font-size: 2.5rem; font-weight: 700;">
                <i class="bi bi-pc-display me-3"></i>
                Manajemen Aset Komputer
            </h1>
            <p style="margin: 0.5rem 0 0 0; opacity: 0.9; font-size: 1.1rem;">
                Sistem pengelolaan aset komputer dengan tampilan yang lebih user-friendly
            </p>
        </div>

        <!-- Category Navigation -->
        <div class="category-nav">
            <a href="#" class="category-item active" data-category="komputer-unit">
                <i class="bi bi-pc-display" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                <strong>Komputer Unit</strong>
            </a>
            <a href="#" class="category-item" data-category="peralatan-komputer">
                <i class="bi bi-keyboard" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                <strong>Peralatan Komputer</strong>
            </a>
        </div>

        <!-- Tab Navigation for Different Views -->
        <div class="tab-nav">
            <button class="tab-btn active" data-tab="cards">
                <i class="bi bi-grid-3x3-gap me-2"></i>
                Card View
            </button>
            <button class="tab-btn" data-tab="table">
                <i class="bi bi-table me-2"></i>
                Table View
            </button>
            <button class="tab-btn" data-tab="detail">
                <i class="bi bi-layout-sidebar me-2"></i>
                Master-Detail
            </button>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Card View -->
            <div class="tab-pane active" id="cards">
                <div class="asset-cards">
                    <!-- Sample Card 1 -->
                    <div class="asset-card">
                        <div class="card-header">
                            <h3 class="card-title">ASUS ZENBOOK PRO OLED5911</h3>
                            <span class="badge badge-primary">KOMPUTER UNIT</span>
                        </div>
                        <div class="card-body">
                            <div class="asset-detail">
                                <div class="detail-item">
                                    <span class="detail-label">Kode Barang</span>
                                    <span class="detail-value">56</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Bidang</span>
                                    <span class="detail-value">BDI</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">NUP</span>
                                    <span class="detail-value">56</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Merk</span>
                                    <span class="detail-value">Lap Top</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Pengguna</span>
                                    <span class="detail-value">Najib Baedlowi</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Kondisi</span>
                                    <span class="badge badge-success">Baik</span>
                                </div>
                            </div>
                            
                            <div class="qr-section">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ASUS-ZENBOOK-PRO-OLED5911" 
                                     class="qr-code" 
                                     alt="QR Code"
                                     onclick="copyToClipboard('ASUS-ZENBOOK-PRO-OLED5911')"
                                     title="Klik untuk copy QR Code">
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">Nilai Perolehan</span>
                                <span class="detail-value text-currency">Rp 35.500.000</span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn btn-primary btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                            <button class="btn btn-outline btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Sample Card 2 -->
                    <div class="asset-card">
                        <div class="card-header">
                            <h3 class="card-title">HP OMEN 16</h3>
                            <span class="badge badge-primary">KOMPUTER UNIT</span>
                        </div>
                        <div class="card-body">
                            <div class="asset-detail">
                                <div class="detail-item">
                                    <span class="detail-label">Kode Barang</span>
                                    <span class="detail-value">54</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Bidang</span>
                                    <span class="detail-value">BDI</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">NUP</span>
                                    <span class="detail-value">54</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Merk</span>
                                    <span class="detail-value">Lap Top</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Pengguna</span>
                                    <span class="detail-value">Muhammad Ibnu Fadinaldi</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Kondisi</span>
                                    <span class="badge badge-success">Baik</span>
                                </div>
                            </div>
                            
                            <div class="qr-section">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=HP-OMEN-16" 
                                     class="qr-code" 
                                     alt="QR Code"
                                     onclick="copyToClipboard('HP-OMEN-16')"
                                     title="Klik untuk copy QR Code">
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">Nilai Perolehan</span>
                                <span class="detail-value text-currency">Rp 41.000.000</span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn btn-primary btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                            <button class="btn btn-outline btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responsive Table View -->
            <div class="tab-pane" id="table">
                <div class="responsive-table-wrapper">
                    <div class="table-controls">
                        <h4 style="margin: 0;">Data Tabel</h4>
                        <div class="column-toggle">
                            <span style="font-size: 0.875rem; font-weight: 500; margin-right: 0.5rem;">Tampilkan kolom:</span>
                            <button class="column-btn active" data-column="processor">Processor</button>
                            <button class="column-btn active" data-column="memory">Memory</button>
                            <button class="column-btn active" data-column="hardisk">Hardisk</button>
                            <button class="column-btn active" data-column="monitor">Monitor</button>
                            <button class="column-btn active" data-column="user-prev">User Sebelumnya</button>
                            <button class="column-btn active" data-column="status">Status</button>
                            <button class="column-btn active" data-column="keterangan">Keterangan</button>
                        </div>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="responsive-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Bidang</th>
                                    <th>Nama Barang</th>
                                    <th>Merk</th>
                                    <th>Pengguna</th>
                                    <th>Kondisi</th>
                                    <th>Nilai</th>
                                    <th>QR Code</th>
                                    <th class="col-processor">Processor</th>
                                    <th class="col-memory">Memory</th>
                                    <th class="col-hardisk">Hardisk</th>
                                    <th class="col-monitor">Monitor</th>
                                    <th class="col-user-prev">User Sebelumnya</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-keterangan">Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><strong>56</strong></td>
                                    <td>BDI</td>
                                    <td><strong>ASUS ZENBOOK PRO OLED5911</strong></td>
                                    <td>Lap Top</td>
                                    <td>Najib Baedlowi</td>
                                    <td><span class="badge badge-success">Baik</span></td>
                                    <td class="text-currency">Rp 35.500.000</td>
                                    <td>
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=ASUS-ZENBOOK-PRO-OLED5911" 
                                             style="width: 40px; height: 40px; cursor: pointer;" 
                                             alt="QR Code"
                                             onclick="copyToClipboard('ASUS-ZENBOOK-PRO-OLED5911')">
                                    </td>
                                    <td class="col-processor">Intel Core i7</td>
                                    <td class="col-memory">16GB</td>
                                    <td class="col-hardisk">512GB SSD</td>
                                    <td class="col-monitor">15.6" OLED</td>
                                    <td class="col-user-prev">-</td>
                                    <td class="col-status"><span class="badge badge-success">Aktif</span></td>
                                    <td class="col-keterangan">Akan ditransfer keluar ke SDA</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm me-1"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-outline btn-sm me-1"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><strong>54</strong></td>
                                    <td>BDI</td>
                                    <td><strong>HP OMEN 16</strong></td>
                                    <td>Lap Top</td>
                                    <td>Muhammad Ibnu Fadinaldi</td>
                                    <td><span class="badge badge-success">Baik</span></td>
                                    <td class="text-currency">Rp 41.000.000</td>
                                    <td>
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=HP-OMEN-16" 
                                             style="width: 40px; height: 40px; cursor: pointer;" 
                                             alt="QR Code"
                                             onclick="copyToClipboard('HP-OMEN-16')">
                                    </td>
                                    <td class="col-processor">AMD Ryzen 7</td>
                                    <td class="col-memory">16GB</td>
                                    <td class="col-hardisk">1TB SSD</td>
                                    <td class="col-monitor">16" IPS</td>
                                    <td class="col-user-prev">-</td>
                                    <td class="col-status"><span class="badge badge-success">Aktif</span></td>
                                    <td class="col-keterangan">-</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm me-1"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-outline btn-sm me-1"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Master-Detail View -->
            <div class="tab-pane" id="detail">
                <div class="master-detail-layout">
                    <div class="master-list">
                        <div class="list-header">
                            Daftar Aset Komputer
                        </div>
                        <div class="list-body">
                            <div class="list-item active" data-item="1">
                                <div class="item-title">ASUS ZENBOOK PRO OLED5911</div>
                                <div class="item-subtitle">BDI • Najib Baedlowi • Rp 35.500.000</div>
                            </div>
                            <div class="list-item" data-item="2">
                                <div class="item-title">HP OMEN 16</div>
                                <div class="item-subtitle">BDI • Muhammad Ibnu Fadinaldi • Rp 41.000.000</div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-panel">
                        <div class="detail-header">
                            Detail Aset
                        </div>
                        <div class="detail-content">
                            <div class="detail-grid">
                                <div style="text-align: center; margin-bottom: 1.5rem;">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=ASUS-ZENBOOK-PRO-OLED5911" 
                                         style="border: 1px solid var(--border-color); border-radius: 4px;"
                                         alt="QR Code">
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Nama Barang</span>
                                    <span class="detail-value">ASUS ZENBOOK PRO OLED5911</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Kode Barang</span>
                                    <span class="detail-value">56</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Bidang</span>
                                    <span class="detail-value">BDI</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Merk</span>
                                    <span class="detail-value">Lap Top</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">NUP</span>
                                    <span class="detail-value">56</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Kelompok</span>
                                    <span class="badge badge-primary">KOMPUTER UNIT</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Processor</span>
                                    <span class="detail-value">Intel Core i7</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Memory</span>
                                    <span class="detail-value">16GB</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Hardisk</span>
                                    <span class="detail-value">512GB SSD</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Monitor</span>
                                    <span class="detail-value">15.6" OLED</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Pengguna Sekarang</span>
                                    <span class="detail-value">Najib Baedlowi</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Kondisi</span>
                                    <span class="badge badge-success">Baik</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Status Penggunaan</span>
                                    <span class="badge badge-success">Aktif</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Nilai Perolehan</span>
                                    <span class="detail-value text-currency">Rp 35.500.000</span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Keterangan</span>
                                    <span class="detail-value">Akan ditransfer keluar ke SDA</span>
                                </div>
                            </div>
                            
                            <div style="margin-top: 2rem; display: flex; gap: 0.5rem;">
                                <button class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-danger">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tab functionality
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all tabs
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                
                // Add active class to clicked tab
                btn.classList.add('active');
                const tabId = btn.dataset.tab;
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Column toggle functionality
        document.querySelectorAll('.column-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const column = btn.dataset.column;
                const isActive = btn.classList.contains('active');
                
                if (isActive) {
                    btn.classList.remove('active');
                    document.querySelectorAll(`.col-${column}`).forEach(col => {
                        col.style.display = 'none';
                    });
                } else {
                    btn.classList.add('active');
                    document.querySelectorAll(`.col-${column}`).forEach(col => {
                        col.style.display = '';
                    });
                }
            });
        });

        // Master-detail functionality
        document.querySelectorAll('.list-item').forEach(item => {
            item.addEventListener('click', () => {
                document.querySelectorAll('.list-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            });
        });

        // Copy to clipboard
        function copyToClipboard(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('QR Code berhasil disalin: ' + text, 'success');
                });
            }
        }

        // Toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
            toast.style.minWidth = '300px';
            toast.innerHTML = `
                <strong>${type === 'success' ? 'Berhasil!' : 'Info!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.getElementById('toast-container').appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 3000);
        }

        // Category navigation
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                document.querySelectorAll('.category-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            });
        });
    </script>
</body>
</html>