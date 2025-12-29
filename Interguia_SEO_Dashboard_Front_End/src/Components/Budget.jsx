import { useEffect, useRef } from "react";
import Chart from "chart.js/auto";

import BudgetStyle from "../styles/components/Budget.module.scss";
import DateMonthYearPicker from "./DateMonthYearPicker";
import Card_Small_TextRight from "./Charts/Card_Small_TextRight";
import Card_Small_TextLeft from "./Charts/Card_Small_TextLeft";
import ComparativeLineChart from "./Charts/ComparativeLineChart";
import Card_Large from "./Charts/Card_Large";
import { FaChartSimple } from "react-icons/fa6";

//ICONS
import { BsBarChartFill } from "react-icons/bs";
import { RiBarChartBoxAiFill } from "react-icons/ri";
import { FaMoneyBillTrendUp } from "react-icons/fa6";
import { FaMoneyCheckDollar } from "react-icons/fa6";

export default function Budget() {
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
  }, []); // runs once

  const scenarios = ["Scenario-1", "Scenario-2", "Scenario-3"];
  return (
    <>
      {/* DATE PICKER AND SCENARIO*/}
      <div className={BudgetStyle.Budget_DatePicker}>
        <div>
          <select className={BudgetStyle.select}>
            {scenarios.map((item, index) => (
              <option className={BudgetStyle.option}>{item}</option>
            ))}
          </select>
        </div>
        <DateMonthYearPicker />
      </div>

      {/* CARDBAR */}
      <div className={BudgetStyle.Budget_CardBar}>
        {/* Card-1 */}
        <Card_Small_TextRight
          icon={<BsBarChartFill />}
          title="Ingreso Total Mensual"
          data="$1500.00"
        />

        {/* Card-2 */}
        <Card_Small_TextLeft
          icon={<RiBarChartBoxAiFill />}
          title="Gasto Total Mensual"
          data="$3250.00"
        />

        {/* Card-3 */}
        <Card_Small_TextRight
          icon={<FaMoneyBillTrendUp />}
          title="Utilidad Neta Mensual"
          data="$2458.00"
        />

        {/* Card-4 */}
        <Card_Small_TextRight
          icon={<FaMoneyCheckDollar />}
          title="Presupuesto Mensual"
          data="$1500.00"
        />
      </div>
      {/* LINECHART Comparativo */}
      <div className={BudgetStyle.Budget_ComparativeChartLine}>
        <div className={BudgetStyle.Budget_ComparativeChartLine_TopBar}>
          {" "}
          <h3 className={BudgetStyle.Budget_ComparativeChartLine_Title}>
            Presupuesto Vs Gastado al Mes
          </h3>
          <div>
            <DateMonthYearPicker />
          </div>
        </div>
        <ComparativeLineChart />
      </div>

      {/* CardLarge */}

      <div className={BudgetStyle.Budget_CardLarge}>
        <Card_Large
          title="Presupuesto Final del Mes Anterio Vs Actual"
          icon={<FaChartSimple />}
          data="$10500.50"
          performance="-25%"
        />
      </div>
      <div className={BudgetStyle.Budget_firstRow}>
        <canvas ref={chartRef}></canvas>
      </div>
    </>
  );
}
