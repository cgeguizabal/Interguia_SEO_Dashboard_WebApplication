

import BudgetStyle from "../../styles/components/Budget.module.scss";
import DateMonthYearPicker from "../DateMonthYearPicker";
import Card_Small_TextRight from "../Charts/Card_Small_TextRight";
import Card_Small_TextLeft from "../Charts/Card_Small_TextLeft";
import ComparativeLineChart from "../Charts/ComparativeLineChart";
import Card_Large from "../Charts/Card_Large";
import { FaChartSimple } from "react-icons/fa6";

//ICONS
import { BsBarChartFill } from "react-icons/bs";
import { RiBarChartBoxAiFill } from "react-icons/ri";
import { FaMoneyBillTrendUp } from "react-icons/fa6";
import { FaMoneyCheckDollar } from "react-icons/fa6";

export default function Budget() {
  

  const scenarios = ["Scenario-1", "Scenario-2", "Scenario-3"];
  //DATOS para grafica  de linebar
  const gastos = [
    1200, 1400, 1350, 1600, 1700, 1800, 1320, 1450, 2500, 1000, 500, 1000,
  ];
  const gastosPresupuestos = [
    900, 1100, 1050, 1200, 1300, 1500, 1000, 2500, 3240, 500, 1000, 1000,
  ];

  const ingresos = [
    1400, 1600, 1200, 2500, 1350, 1700, 1800, 1320, 1450, 1000, 500, 1000,
  ];
  const ingresosPresupuestos = [
    2500, 1100, 1050, 1200, 1500, 1000, 3240, 900, 500, 1000, 1000, 1300,
  ];

  return (
    <>
      {/* DATE PICKER AND SCENARIO*/}
      <div className={BudgetStyle.Budget_DatePicker}>
        <div>
          <select className={BudgetStyle.select}>
            {scenarios.map((item, index) => (
              <option className={BudgetStyle.option} key={index}>
                {item}
              </option>
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
        <Card_Small_TextLeft
          icon={<FaMoneyCheckDollar />}
          title="Presupuesto Mensual"
          data="$1500.00"
        />
      </div>
      
      {/* LINECHART Comparativo-1 */}
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
        <ComparativeLineChart
          label1="Gastos"
          label2="Presupuestos"
          data1={gastos}
          data2={gastosPresupuestos}
        />
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

      {/* LINECHART Comparativo-2 */}
      <div className={BudgetStyle.Budget_ComparativeChartLine_2}>
        <div className={BudgetStyle.Budget_ComparativeChartLine_TopBar_2}>
          {" "}
          <h3 className={BudgetStyle.Budget_ComparativeChartLine_Title_2}>
            Presupuesto Vs Ingreso al Mes
          </h3>
          <div>
            <DateMonthYearPicker />
          </div>
        </div>
        <ComparativeLineChart
          label1="Ingresos"
          label2="Presupuestos"
          data1={ingresos}
          data2={ingresosPresupuestos}
        />
      </div>

      {/* Card-5 */}
      <div className={BudgetStyle.Budget_CardSmall_5}>
        <Card_Small_TextLeft
          icon={<FaMoneyCheckDollar />}
          title="Gastos Totales Anuales"
          data="$1400.00"
        />
      </div>

      {/* Card-6 */}
      <div className={BudgetStyle.Budget_CardSmall_6}>
        <Card_Small_TextRight
          icon={<BsBarChartFill />}
          title="Ingresos Totales Anuales"
          data="$2500.00"
        />
      </div>

      {/* CARD-7  */}
      <div className={BudgetStyle.Budget_CardBar_2}>
        <div className={BudgetStyle.Budget_CardBar_2_Card}>
          <h3 className={BudgetStyle.Budget_CardBar_2_Card_title}>
            Ingreso mes Actual
          </h3>
          <h2 className={BudgetStyle.Budget_CardBar_2_Card_data}>$1500</h2>
        </div>
        <div className={BudgetStyle.Budget_CardBar_2_Card}>
          <h3 className={BudgetStyle.Budget_CardBar_2_Card_title}>
            Presupuesto Actual
          </h3>
          <h2 className={BudgetStyle.Budget_CardBar_2_Card_data}>$1800</h2>
        </div>
      </div>
    </>
  );
}
