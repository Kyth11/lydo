document.addEventListener("DOMContentLoaded", function () {
    const data = window.dashboardData;

    /* =========================
    BARANGAY BAR CHART
    ========================= */

    new Chart(document.getElementById("barangayChart"), {
        type: "bar",

        data: {
            labels: data.barangayLabels,

            datasets: [
                {
                    label: "Male",
                    data: data.maleData,
                    backgroundColor: "#3b82f6",
                    borderRadius: 6,
                },
                {
                    label: "Female",
                    data: data.femaleData,
                    backgroundColor: "#ec4899",
                    borderRadius: 6,
                },
                {
                    label: "Total",
                    data: data.totalData,
                    backgroundColor: "#50C878",
                    borderRadius: 6,
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: { position: "top" },
            },

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, stepSize: 1 },
                },
            },
        },
    });

    /* =========================
    AGE GROUP CHART (NEW)
    ========================= */

    if (data.ageGroups) {
        const ageLabels = ["15-17", "18-21", "22-25", "26-30"];

        const ageCounts = [
            data.ageGroups["15-17"] || 0,
            data.ageGroups["18-21"] || 0,
            data.ageGroups["22-25"] || 0,
            data.ageGroups["26-30"] || 0,
        ];

        const ageCanvas = document.getElementById("ageGroupChart");

        if (ageCanvas) {
            new Chart(ageCanvas, {
                type: "bar",

                data: {
                    labels: ageLabels,

                    datasets: [
                        {
                            label: "Youth Count",

                            data: ageCounts,

                            backgroundColor: [
                                "#22c55e",
                                "#3b82f6",
                                "#a855f7",
                                "#f97316",
                            ],

                            borderRadius: 6,
                        },
                    ],
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: { display: false },
                    },

                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                        },
                    },
                },
            });
        }
    }

    /* =========================
    CENTER TEXT PLUGIN
    ========================= */

    const centerTextPlugin = {
        id: "centerText",

        beforeDraw(chart) {
            if (chart.config.type !== "doughnut") return;

            const { width, height, ctx } = chart;
            const dataset = chart.data.datasets[0].data;

            const total = dataset.reduce((a, b) => a + b, 0);
            if (!total) return;

            const malePercent = Math.round((dataset[0] / total) * 100);
            const femalePercent = Math.round((dataset[1] / total) * 100);

            ctx.restore();
            ctx.font = "bold 8px sans-serif";
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillStyle = "#374151";

            ctx.fillText(
                malePercent + "% M / " + femalePercent + "% F",
                width / 2,
                height / 2,
            );

            ctx.save();
        },
    };

    Chart.register(centerTextPlugin);

    /* =========================
    ALL BARANGAY PIE
    ========================= */

    new Chart(document.getElementById("pieChartAll"), {
        type: "doughnut",

        data: {
            labels: ["Male", "Female"],
            datasets: [
                {
                    data: [data.maleTotal, data.femaleTotal],
                    backgroundColor: ["#3b82f6", "#ec4899"],
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "65%",
            plugins: { legend: { display: false } },
        },
    });

    /* =========================
    PIE CHARTS PER BARANGAY
    ========================= */

    data.barangayGenderData.forEach((b, index) => {
        new Chart(document.getElementById("pieChart" + index), {
            type: "doughnut",

            data: {
                labels: ["Male", "Female"],
                datasets: [
                    {
                        data: [b.male, b.female],
                        backgroundColor: ["#3b82f6", "#ec4899"],
                    },
                ],
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "65%",
                plugins: { legend: { display: false } },
            },
        });
    });

    /* =========================
    MUNICIPAL COVERAGE CHART
    ========================= */

    new Chart(document.getElementById("coverageChart"), {
        type: "bar",

        data: {
            labels: [
                "Total Youth Population",
                "Profiles Added",
                "Remaining Youth",
            ],

            datasets: [
                {
                    data: [
                        data.totalPopulation,
                        data.totalProfiles,
                        data.totalPopulation - data.totalProfiles,
                    ],

                    backgroundColor: ["#6366f1", "#22c55e", "#f97316"],

                    borderRadius: 8,
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ctx.raw + " youth";
                        },
                    },
                },
            },

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                },
            },
        },
    });

    /* =========================
    BARANGAY COVERAGE CHART
    ========================= */

    const labels = data.barangayCoverage.map((b) => b.barangay);
    const values = data.barangayCoverage.map((b) => b.percent);

    const colors = data.barangayCoverage.map((b) => {
        if (b.percent <= 30) return "#ef4444";
        if (b.percent <= 60) return "#facc15";
        return "#22c55e";
    });

    new Chart(document.getElementById("barangayCoverageChart"), {
        type: "bar",

        data: {
            labels: labels,

            datasets: [
                {
                    data: values,
                    backgroundColor: colors,
                    borderRadius: 6,
                },
            ],
        },

        options: {
            indexAxis: "y",

            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            const item = data.barangayCoverage[ctx.dataIndex];

                            const profiles = item.profiles;
                            const total = item.population;
                            const percent = item.percent;

                            return `${profiles}/${total} (${percent}%) coverage`;
                        },
                    },
                },
            },

            scales: {
                x: {
                    max: 100,
                    beginAtZero: true,
                    ticks: {
                        callback: function (v) {
                            return v + "%";
                        },
                    },
                },
            },
        },
    });

    /* =========================
    ANNOUNCEMENT SLIDER
    ========================= */

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const strip = document.getElementById("announcementStrip");
    const slider = document.getElementById("announcementSlider");
    const dotsContainer = document.getElementById("announcementDots");

    if (!slider) return;

    let slides = Array.from(slider.querySelectorAll(".announcement-slide"));

    slides.forEach((slide) => {
        const endDate = slide.dataset.end;

        if (endDate) {
            const end = new Date(endDate);
            end.setHours(0, 0, 0, 0);

            if (end < today) {
                slide.remove();
            }
        }
    });

    slides = Array.from(slider.querySelectorAll(".announcement-slide"));

    if (slides.length === 0) {
        strip.style.display = "none";
        return;
    }

    dotsContainer.innerHTML = "";

    slides.forEach((slide, index) => {
        const dot = document.createElement("span");
        dot.className = "dot";
        dot.dataset.index = index;

        if (index === 0) {
            slide.classList.add("active");
            dot.classList.add("active");
        }

        dotsContainer.appendChild(dot);
    });

    const dots = dotsContainer.querySelectorAll(".dot");

    let current = 0;
    let interval;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle("active", i === index);

            if (dots[i]) {
                dots[i].classList.toggle("active", i === index);
            }
        });

        current = index;
    }

    function startSlider() {
        if (slides.length <= 1) return;

        interval = setInterval(() => {
            let next = (current + 1) % slides.length;
            showSlide(next);
        }, 4000);
    }

    startSlider();

    dots.forEach((dot) => {
        dot.addEventListener("click", function () {
            clearInterval(interval);
            showSlide(parseInt(this.dataset.index));
            startSlider();
        });
    });
});
