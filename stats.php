<?php
session_start();
require_once 'includes/db.php';

$page_title = 'Statistics - InsectaBase';
require_once 'includes/header.php';

$species_list = $pdo->query("SELECT * FROM species WHERE latitude IS NOT NULL AND longitude IS NOT NULL")->fetchAll();
?>

<!-- ✅ Include Bootstrap if not already done -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    #cesiumContainer {
        width: 100%;
        height: 85vh;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    .species-table-container {
        height: 85vh;
        overflow-y: auto;
        padding: 1.5rem;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .location-link {
        color: #0d6efd;
        cursor: pointer;
        font-weight: 500;
        text-decoration: underline;
    }

    .text-truncate {
        max-width: 180px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .table th, .table td {
        vertical-align: middle;
    }

    .table a {
        color: #0d6efd;
        text-decoration: none;
    }

    .table a:hover {
        text-decoration: underline;
    }
</style>

<div class="container-fluid my-4">
    <div class="text-center mb-4">
        <h1>InsectaBase Statistics</h1>
        <p class="lead">Click a location in the table to fly to it on the globe.</p>
    </div>

    <div class="row">
        <!-- 🌍 Cesium Globe -->
        <div class="col-lg-7 mb-4">
            <div id="cesiumContainer"></div>
        </div>

        <!-- 📊 Table Column -->
        <div class="col-lg-5">
            <div class="species-table-container">

                <!-- 🔍 Search -->
                <input type="text" id="searchInput" class="form-control mb-3" placeholder="🔍 Search species...">

                <!-- 🧾 Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle text-center">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>Species</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Diagnosis</th>
                            </tr>
                        </thead>
                        <tbody id="speciesTableBody">
                            <?php if (count($species_list) > 0): ?>
                                <?php foreach ($species_list as $species): ?>
                                    <tr>
                                        <td>
                                            <a href="species.php?id=<?= $species['id'] ?>">
                                                <?= htmlspecialchars($species['name'] ?? '') ?>
                                            </a>
                                        </td>
                                        <td class="location-link"
                                            data-lat="<?= $species['latitude'] ?>"
                                            data-lon="<?= $species['longitude'] ?>">
                                            <?= htmlspecialchars($species['location'] ?? '') ?>
                                        </td>
                                        <td><?= htmlspecialchars($species['status'] ?? '') ?></td>
                                        <td class="text-truncate" title="<?= htmlspecialchars($species['description'] ?? '') ?>">
                                            <?= htmlspecialchars($species['description'] ?? '') ?>
                                        </td>
                                        <td class="text-truncate" title="<?= htmlspecialchars($species['diagnosis'] ?? '') ?>">
                                            <?= htmlspecialchars($species['diagnosis'] ?? '') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">No species found with location data.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- 🌐 Cesium JS -->
<script src="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Cesium.js"></script>

<script>
    Cesium.Ion.defaultAccessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiI0YzAxMTg0ZC02ZTZlLTRlMDktOTM2ZC0zNTZjZDc3OTI3YjYiLCJpZCI6MzI3NDM0LCJpYXQiOjE3NTM5NTY1ODB9.0PvLZOZTmYwCGEyEIWWxnoPQ0hpkKSETpkjCX5x5r-k';

    const viewer = new Cesium.Viewer('cesiumContainer', {
        animation: false,
        timeline: false,
        geocoder: false,
        homeButton: false,
        sceneModePicker: false,
        baseLayerPicker: false,
        navigationHelpButton: false
    });

    // 🌍 Fly to location
    document.querySelectorAll('.location-link').forEach(link => {
        link.addEventListener('click', () => {
            const lat = parseFloat(link.dataset.lat);
            const lon = parseFloat(link.dataset.lon);
            if (!isNaN(lat) && !isNaN(lon)) {
                viewer.camera.flyTo({
                    destination: Cesium.Cartesian3.fromDegrees(lon, lat, 1500000),
                    orientation: {
                        heading: Cesium.Math.toRadians(0.0),
                        pitch: Cesium.Math.toRadians(-60.0),
                    },
                    duration: 3
                });
            }
        });
    });

    // 🔍 Live Search
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#speciesTableBody tr").forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>
