//PRESUPUESTOS

import BudgetStyle from '../styles/components/Budget.module.scss';
import DateMonthYearPicker from './DateMonthYearPicker';


export default function Budget() {
  return (
    <>
    <div className={BudgetStyle.Budget_DatePicker}><DateMonthYearPicker /></div>
    <div className={BudgetStyle.Budget_firstRow}>FirsRow</div>
    </>
    )
  
}


