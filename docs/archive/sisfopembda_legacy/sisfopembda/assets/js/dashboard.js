// SISFOPEMBDA Dashboard JavaScript

class Dashboard {
  constructor() {
    this.charts = {};
    this.init();
  }

  init() {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", () => {
        this.loadDashboardData();
        this.initCharts();
        this.setupRefresh();
      });
    } else {
      // Document already loaded
      this.loadDashboardData();
      this.initCharts();
      this.setupRefresh();
    }
  }

  async loadDashboardData() {
    try {
      // Show loading state
      this.setLoadingState(true);

      const response = await fetch("get_dashboard_data.php");
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }

      const data = await response.json();

      // Update statistics
      this.updateStatistics(data);

      // Update recent penugasan table
      this.updateRecentPenugasan(data.recentPenugasan || []);

      // Update charts
      this.updateCharts(data);

      // Hide loading state
      this.setLoadingState(false);
    } catch (error) {
      console.error("Error loading dashboard data:", error);
      this.showError("Gagal memuat data dashboard");
      this.setLoadingState(false);
    }
  }

  updateStatistics(data) {
    const stats = [
      { id: "totalPegawai", value: data.totalPegawai || 0 },
      { id: "totalUnit", value: data.totalUnit || 0 },
      { id: "totalPenugasan", value: data.totalPenugasan || 0 },
      { id: "totalHonor", value: this.formatCurrency(data.totalHonor || 0) },
    ];

    stats.forEach((stat) => {
      const element = document.getElementById(stat.id);
      if (element) {
        this.animateValue(element, stat.value);
      }
    });
  }

  animateValue(element, targetValue) {
    if (typeof targetValue === "string") {
      element.textContent = targetValue;
      return;
    }

    const startValue = parseInt(element.textContent) || 0;
    const duration = 1000;
    const startTime = performance.now();

    const animate = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);

      const currentValue = Math.floor(
        startValue + (targetValue - startValue) * progress
      );
      element.textContent = currentValue.toLocaleString("id-ID");

      if (progress < 1) {
        requestAnimationFrame(animate);
      }
    };

    requestAnimationFrame(animate);
  }

  updateRecentPenugasan(data) {
    const tbody = document.getElementById("recentPenugasan");
    if (!tbody) return;

    tbody.innerHTML = "";

    if (data.length === 0) {
      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center text-muted">Tidak ada data penugasan</td></tr>';
      return;
    }

    data.forEach((item) => {
      const row = tbody.insertRow();
      row.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar me-2">
                            <i class="fas fa-user-circle fa-lg text-muted"></i>
                        </div>
                        <div>
                            <div class="fw-bold">${this.escapeHtml(
                              item.nama_pegawai
                            )}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-secondary">${this.escapeHtml(
                      item.nama_unit
                    )}</span>
                </td>
                <td>${this.escapeHtml(item.tahun_pelajaran)}</td>
                <td>
                    <span class="badge bg-info">${item.jam_mengajar} jam</span>
                </td>
                <td class="fw-bold text-success">${this.formatCurrency(
                  item.total
                )}</td>
            `;
    });
  }

  initCharts() {
    this.initPegawaiChart();
    this.initStatusChart();
  }

  initPegawaiChart() {
    const ctx = document.getElementById("pegawaiChart");
    if (!ctx) return;

    this.charts.pegawai = new Chart(ctx.getContext("2d"), {
      type: "doughnut",
      data: {
        labels: [],
        datasets: [
          {
            data: [],
            backgroundColor: [
              "#667eea",
              "#764ba2",
              "#11998e",
              "#38ef7d",
              "#f093fb",
              "#f5576c",
              "#4facfe",
              "#00f2fe",
            ],
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              padding: 20,
              usePointStyle: true,
            },
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.label || "";
                const value = context.parsed;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((value / total) * 100).toFixed(1);
                return `${label}: ${value} (${percentage}%)`;
              },
            },
          },
        },
      },
    });
  }

  initStatusChart() {
    const ctx = document.getElementById("statusChart");
    if (!ctx) {
      console.error("Status chart element not found!");
      return;
    }

    console.log("Initializing status chart...");

    // Define colors for each status according to getStatusBadge() function
    const statusColors = {
      PNS: "#198754", // bg-success (green)
      GTY: "#0d6efd", // bg-primary (blue)
      PTY: "#0dcaf0", // bg-info (cyan)
      Kontrak: "#ffc107", // bg-warning (yellow)
      Honorer: "#6c757d", // bg-secondary (gray)
      Percobaan: "#dc3545", // bg-danger (red)
    };

    this.charts.status = new Chart(ctx.getContext("2d"), {
      type: "bar",
      data: {
        labels: [],
        datasets: [
          {
            label: "Jumlah Pegawai",
            data: [],
            backgroundColor: [], // Will be populated dynamically
            borderColor: [], // Will be populated dynamically
            borderWidth: 1,
            borderRadius: 4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
            },
          },
        },
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                return `${context.dataset.label}: ${context.parsed.y} orang`;
              },
            },
          },
        },
      },
    });

    console.log("Status chart initialized:", this.charts.status);
  }

  updateCharts(data) {
    // Update pegawai per unit chart
    if (data.pegawaiPerUnit && this.charts.pegawai) {
      this.charts.pegawai.data.labels = data.pegawaiPerUnit.labels;
      this.charts.pegawai.data.datasets[0].data =
        data.pegawaiPerUnit.data.map(Number);
      this.charts.pegawai.update("active");
    }

    // Update status kepegawaian chart with proper colors
    if (data.statusKepegawaian && this.charts.status) {
      // Define colors for each status according to getStatusBadge() function
      const statusColors = {
        PNS: "#198754", // bg-success (green)
        GTY: "#0d6efd", // bg-primary (blue)
        PTY: "#0dcaf0", // bg-info (cyan)
        Kontrak: "#ffc107", // bg-warning (yellow)
        Honorer: "#6c757d", // bg-secondary (gray)
        Percobaan: "#dc3545", // bg-danger (red)
      };

      console.log("Status data received:", data.statusKepegawaian);

      // Assign colors based on status labels
      const backgroundColors = [];
      const borderColors = [];

      data.statusKepegawaian.labels.forEach((label) => {
        const color = statusColors[label] || "#6c757d"; // Default to gray if status not found
        console.log(`Status: ${label}, Color: ${color}`);
        backgroundColors.push(color);
        borderColors.push(color);
      });

      console.log("Background colors:", backgroundColors);

      // Destroy existing chart and recreate with new colors
      this.charts.status.destroy();

      const ctx = document.getElementById("statusChart");
      this.charts.status = new Chart(ctx.getContext("2d"), {
        type: "bar",
        data: {
          labels: data.statusKepegawaian.labels,
          datasets: [
            {
              label: "Jumlah Pegawai",
              data: data.statusKepegawaian.data.map(Number),
              backgroundColor: backgroundColors,
              borderColor: borderColors,
              borderWidth: 1,
              borderRadius: 4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
              },
            },
          },
          plugins: {
            legend: {
              display: false,
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return `${context.dataset.label}: ${context.parsed.y} orang`;
                },
              },
            },
          },
        },
      });

      console.log("Chart recreated successfully");
    }
  }

  setLoadingState(isLoading) {
    const elements = [
      "totalPegawai",
      "totalUnit",
      "totalPenugasan",
      "totalHonor",
    ];

    elements.forEach((id) => {
      const element = document.getElementById(id);
      if (element) {
        if (isLoading) {
          element.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
      }
    });
  }

  setupRefresh() {
    // Auto refresh every 5 minutes
    setInterval(() => {
      this.loadDashboardData();
    }, 300000);

    // Manual refresh button
    const refreshBtn = document.getElementById("refreshData");
    if (refreshBtn) {
      refreshBtn.addEventListener("click", () => {
        this.loadDashboardData();
      });
    }
  }

  formatCurrency(amount) {
    return "Rp " + parseInt(amount || 0).toLocaleString("id-ID");
  }

  escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  showError(message) {
    // Simple error notification
    const alertDiv = document.createElement("div");
    alertDiv.className =
      "alert alert-danger alert-dismissible fade show position-fixed";
    alertDiv.style.cssText =
      "top: 20px; right: 20px; z-index: 9999; max-width: 300px;";
    alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

    document.body.appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
      if (alertDiv.parentNode) {
        alertDiv.remove();
      }
    }, 5000);
  }
}

// Initialize dashboard
const dashboard = new Dashboard();
