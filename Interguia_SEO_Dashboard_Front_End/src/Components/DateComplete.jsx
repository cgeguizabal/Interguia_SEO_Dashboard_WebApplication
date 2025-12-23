import DateMonthYearPickerStyles from "../styles/components/DateMonthYearPicker.module.scss";

export default function DateComplete() {
  const Months = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];

    const StartYear = 1980;
    const CurrentYear = new Date().getFullYear();
  return (
    <>
      {/*  Mes Inicio  */}
      <select className={DateMonthYearPickerStyles.select}>
                  {Months.map((month, index) => (
                      <option className={DateMonthYearPickerStyles.option} key={index} value={month}>{month}</option>
                  ))}
              </select>
      {/* Mes Fin */}
      <select className={DateMonthYearPickerStyles.select}>
                  {Months.map((month, index) => (
                      <option className={DateMonthYearPickerStyles.option} key={index} value={month}>{month}</option>
                  ))}
              </select>
      {/* Año  */}
              <select className={DateMonthYearPickerStyles.select}>
                  {Array.from({ length: CurrentYear - StartYear + 1}, (_, i) => CurrentYear - i).map(year => 
                  (<option className={DateMonthYearPickerStyles.option}
                  key={year} value={year}>{year}</option>))}
              </select>
    
    </>
  )
}
