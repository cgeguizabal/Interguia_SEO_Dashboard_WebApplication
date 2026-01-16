import React from 'react'
import { useEffect, useRef } from "react";
import Chart from "chart.js/auto";

export default function LineBarVertical() {
    const chartRef = useRef(null); // ref for the canvas
      const chartInstanceRef = useRef(null); // ref for the Chart instance
    
      useEffect(() => {
        const ctx = chartRef.current;
    
        if (ctx) {
          // Destroy previous chart if it exists
          if (chartInstanceRef.current) {
            chartInstanceRef.current.destroy();
          }
    
          chartInstanceRef.current = new Chart(ctx, {
            type: "bar",
            data: {
              labels: [
                "Red",
                "Blue",
                "Yellow",
                "Green",
                "Purple",
                "Orange",
                "test",
              ],
              datasets: [
                {
                  label: "# of Votes",
                  data: [12, 18, 3, 5, 2, 25, 19],
                  backgroundColor: [
                    "rgb(82, 145, 197)",
                    "rgb(187, 213, 228)",
                    "rgb(82, 145, 197)",
                    "rgb(187, 213, 228)",
                    "rgb(82, 145, 197)",
                    "rgb(187, 213, 228)",
                  ],
                  // borderColor: [
                  //   'rgb(187, 213, 228)',
                  //   'rgb(187, 213, 228)',
                  //   'rgb(187, 213, 228)',
                  //   'rgb(187, 213, 228)',
                  //   'rgb(187, 213, 228)',
                  //   'rgb(187, 213, 228)',
                  // ],
                  borderWidth: 2,
                  borderRadius: 5,
                  hoverBackgroundColor: "rgb(187, 213, 228)",
                },
              ],
            },
            options: {
              responsive: true,
    
              animation: {
                duration: 1200,
                easing: "easeOutBounce",
              },
    
              animations: {
                y: {
                  from: (ctx) => ctx.chart.scales.y.getPixelForValue(0),
                },
                base: {
                  from: (ctx) => ctx.chart.scales.y.getPixelForValue(0),
                },
              },
    
              plugins: {
                legend: {
                  labels: {
                    font: {
                      family: "Roboto, sans-serif",
                      size: 14,
                      weight: "500",
                    },
                  },
                },
                tooltip: {
                  bodyFont: {
                    family: "Roboto, sans-serif",
                    size: 13,
                    weight: "400",
                  },
                  titleFont: {
                    family: "Roboto, sans-serif",
                    size: 14,
                    weight: "600",
                  },
                },
              },
    
              scales: {
                x: {
                  grid: {
                    color: "rgb(187, 213, 228)",
                  },
                  ticks: {
                    color: "rgb(163, 174, 208)",
                  },
                },
                y: {
                  beginAtZero: true,
                  grid: {
                    color: "rgb(187, 213, 228)",
                  },
                  ticks: {
                    color: "#040b59",
                  },
                },
              },
            },
            backgroundColor: "rgba(0,0,0,0.1)", // Chart background
          });
        }
    
        // Cleanup on unmount
        return () => {
          if (chartInstanceRef.current) {
            chartInstanceRef.current.destroy();
          }
        };
      }, []); // runs once on mount
  return (
    <div>LineBarVertical</div>
  )
}
