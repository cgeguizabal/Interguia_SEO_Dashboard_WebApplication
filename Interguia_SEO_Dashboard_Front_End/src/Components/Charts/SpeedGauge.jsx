import { useEffect, useRef } from 'react';
import Chart from 'chart.js/auto';
import styles from '../../styles/components/SpeedGauge.module.scss';

export default function SpeedGauge({ speed = 50, chart }) {
  const canvasRef = useRef(null);
  const chartRef = useRef(null);

  useEffect(() => {
    const ctx = canvasRef.current;

    if (chartRef.current) {
      chartRef.current.destroy();
    }

    
  const needlePlugin = {
  id: 'needle',
  afterDatasetDraw(chart) {
    const { ctx } = chart;
    const meta = chart.getDatasetMeta(0);
    const centerX = meta.data[0].x;
    const centerY = meta.data[0].y;

    const minValue = 0.0;
    const maxValue = 4.0;

    // Half-circle from left (-90°) to right (+90°)
    const startAngle = -Math.PI / 2; // -90°
    const endAngle = Math.PI / 2;    // +90°

    const fraction = (speed - minValue) / (maxValue - minValue);
    const angle = startAngle + fraction * (endAngle - startAngle);

    ctx.save();
    ctx.translate(centerX, centerY);
    ctx.rotate(angle);

    ctx.beginPath();
    ctx.moveTo(0, -5);
    ctx.lineTo(0, -80);
    ctx.lineWidth = 3;
    ctx.strokeStyle = '#5291c5';
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(0, 0, 6, 0, Math.PI * 2);
    ctx.fillStyle = '#5291c5';
    ctx.fill();

    ctx.restore();
  }
};



    chartRef.current = new Chart(ctx, {
      type: 'doughnut',
      data: {
        datasets: [
          {
            data: [30, 40, 30],
            backgroundColor: [
              '#5291c5', // green
              '#6aade4ff', // yellow
              '#76bcf5ff'  // red
            ],
            borderWidth: 0,
            // borderRadius: 20 
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        rotation: -90,
        circumference: 180,
        cutout: '70%',
        plugins: {
          legend: { display: false },
          tooltip: { enabled: false }
        }
      },
      plugins: [needlePlugin]
    });

    return () => {
      chartRef.current?.destroy();
    };
  }, [speed]);

  return (
    <div className={styles.gauge}>
      <canvas ref={canvasRef} />
      <div className={styles.text_container}>
      <span className={styles.value}>{speed}</span>
      <p className={styles.title}>{chart}</p>
      </div>
    </div>
  );
}
