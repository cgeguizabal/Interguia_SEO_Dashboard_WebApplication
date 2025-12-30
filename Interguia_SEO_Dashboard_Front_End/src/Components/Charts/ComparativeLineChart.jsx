import { useEffect, useRef } from 'react';
import Chart from 'chart.js/auto';

export default function ComparativeLineChart({label1,label2, data1, data2}) {
  const canvasRef = useRef(null);
  const chartRef = useRef(null);

  useEffect(() => {
    const ctx = canvasRef.current;

    if (chartRef.current) {
      chartRef.current.destroy();
    }

    chartRef.current = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', "Ago", "Sep", "Oct", "Nov", "Dic" ],
        datasets: [
          {
            label: label1,
            data: data1,
            borderColor: 'rgb(82, 145, 197)',
            backgroundColor: 'rgba(82, 145, 197, 0.15)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            
          },
          {
            label: label2,
            data: data2,
            borderColor: 'rgb(187, 213, 228)',
            backgroundColor: 'rgba(187, 213, 228, 0.15)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
          },
        ],
      },
      options: {
        responsive: true,
        interaction: {
          mode: 'index',
          intersect: false,
        },
        plugins: {
          legend: {
            labels: {
              color: '#040b59',
              font: {
                size: 14,
                weight: '500',
              },
            },
          },
          tooltip: {
            backgroundColor: '#0b132b',
            titleColor: '#fff',
            bodyColor: '#fff',
            padding: 10,
          },
        },
        scales: {
          x: {
            ticks: {
              color: '#a3aed0',
            },
            grid: {
              color: 'rgba(255,255,255,0.15)',
            },
          },
          y: {
            beginAtZero: true,
            ticks: {
              color: '#a3aed0',
            },
            grid: {
              color: 'rgba(255,255,255,0.15)',
            },
          },
        },
        animations: {
          y: {
            from: 0,
          },
        },
      },
    });

    return () => chartRef.current?.destroy();
  }, [data1, data2, label1, label2]);

  return <canvas ref={canvasRef} />;
}
