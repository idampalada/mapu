document.addEventListener("DOMContentLoaded", function () {
  const ctxKendaraan = document.getElementById("peminjamanKendaraanChart");
  const ctxRuangan = document.getElementById("peminjamanRuanganChart");

  const bulanLabels = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "Mei",
    "Jun",
    "Jul",
    "Agu",
    "Sep",
    "Okt",
    "Nov",
    "Des",
  ];
  const kendaraanData = Array(12).fill(0);
  const ruanganData = Array(12).fill(0);

  // Fetch data kendaraan
  fetch("/mainpage/getStatistikKendaraanAPI")
    .then((res) => res.json())
    .then((data) => {
      // Kalau API pakai return $this->response->setJSON($query->getResult());
      (data || []).forEach((d) => {
        // label format: '2025-04', ambil bulannya (04 → 3)
        const monthIndex = parseInt(d.label.split("-")[1], 10) - 1;
        kendaraanData[monthIndex] = parseInt(d.jumlah);
      });

      new Chart(ctxKendaraan, {
        type: "line",
        data: {
          labels: bulanLabels,
          datasets: [
            {
              label: "Jumlah Peminjaman",
              data: kendaraanData,
              backgroundColor: "rgba(54, 162, 235, 0.2)",
              borderColor: "rgba(54, 162, 235, 1)",
              borderWidth: 2,
              fill: true,
              tension: 0.4,
              pointRadius: 4,
              pointBackgroundColor: "#369",
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: "Grafik Peminjaman Kendaraan 2025",
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 5 },
            },
          },
        },
      });
    });

  // Fetch data ruangan
  fetch("/mainpage/getStatistikRuanganAPI")
    .then((res) => res.json())
    .then((data) => {
      (data || []).forEach((d) => {
        const monthIndex = parseInt(d.label.split("-")[1], 10) - 1;
        ruanganData[monthIndex] = parseInt(d.jumlah);
      });

      new Chart(ctxRuangan, {
        type: "line",
        data: {
          labels: bulanLabels,
          datasets: [
            {
              label: "Jumlah Peminjaman",
              data: ruanganData,
              backgroundColor: "rgba(255, 206, 86, 0.2)",
              borderColor: "rgba(255, 206, 86, 1)",
              borderWidth: 2,
              fill: true,
              tension: 0.4,
              pointRadius: 4,
              pointBackgroundColor: "#c90",
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: "Grafik Peminjaman Ruangan 2025",
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 5 },
            },
          },
        },
      });
    });
});
