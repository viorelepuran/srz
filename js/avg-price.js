const colors = [
        "#673ab7", // Color for Garsoniera
        "#ffeb3b", // Color for Apartament 2 camere
        "#4caf50", // Color for Apartament 3 camere
        // Add more colors if you have other types...
        "#5bccff", // Color for Media totala
        "#212c3a", // Color for Apartament 2 camere
        "#EE9721", // Color for Apartament 3 camere
      ];

      let labels = rawData.map((item) => item.luna);
      let types = rawData[0].data.map((item) => item.tip);
      let totalData = rawData.map((item) => item.total_average);

      let datasets = types.map((type, index) => {
        let dataForType = rawData.map((monthData) => {
          let dataItem = monthData.data.find((item) => item.tip === type);
          return dataItem ? dataItem.average : null;
        });

        return {
          label: type,
          data: dataForType,
          fill: false,
          borderColor: colors[index],
          tension: 0.1,
        };
      });

 
      let ctx = document.getElementById("myChart").getContext("2d");

      let allValues = rawData.flatMap((month) => [
        ...month.data.map((item) => item.average),
        month.total_average,
      ]);

      let minAverage = Math.min(...allValues);
      let maxAverage = Math.max(...allValues);

      let chart = new Chart(ctx, {
        type: "line",
        data: {
          labels: labels,
          datasets: datasets,
        },
        options: {
          scales: {
            y: {
              min: minAverage - 0.1 * minAverage,
              max: maxAverage + 0.1 * maxAverage,
            },
          },
          tooltips: {
            callbacks: {
              label: function (tooltipItem) {
                if (
                  !tooltipItem ||
                  !this._chart.data ||
                  !this._chart.data.datasets ||
                  !this._chart.data.labels
                ) {
                  return "";
                }

                let type =
                  this._chart.data.datasets[tooltipItem.datasetIndex].label;
                let month = this._chart.data.labels[tooltipItem.index];
                let monthData = rawData.find((item) => item.luna === month);

                if (!monthData) {
                  return "";
                }

                let dataItem = monthData.data.find((item) => item.tip === type);

                if (!dataItem) {
                  return "";
                }

                return `${type}: Tranzactii: ${
                  dataItem.tranzactii
                }, Average: €${dataItem.average.toLocaleString("en-US", {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                })}`;
              },
            },
          },
          plugins: {
            legend: {
              onClick: function (e, legendItem, legend) {
                const index = legendItem.datasetIndex;
                const ci = legend.chart;

                // If the clicked label is not "Total", toggle visibility
                if (ci.data.datasets[index].label !== "Media totala") {
                  if (ci.isDatasetVisible(index)) {
                    ci.setDatasetVisibility(index, false);
                    legendItem.hidden = true;
                  } else {
                    ci.setDatasetVisibility(index, true);
                    legendItem.hidden = false;
                  }
                }

                // We added the call to `update` to ensure that the changes are reflected immediately
                ci.update();
              },
            },
          },
        },
      });