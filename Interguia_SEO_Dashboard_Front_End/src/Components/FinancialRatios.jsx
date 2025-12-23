//RATIOS FINANCIEROS

import { useState } from 'react';
import FinancialRatiosStyle from '../styles/components/FinancialRatios.module.scss';
import Liquidity from './FinacialRatiosComponents/Liquidity';
import Availability from './FinacialRatiosComponents/Availability';
import Rentability from './FinacialRatiosComponents/Rentability';
import Efficiency from './FinacialRatiosComponents/Efficiency';
import Indebtedness from './FinacialRatiosComponents/Indebtedness';
import DateComplete from './DateComplete';

export default function FinancialRatios() {

  const [selectedMenu, setSelectedMenu] = useState(0);

  const handleMenuClick = (index) => {
    setSelectedMenu(index);
  }

  const MenuItems = ['Liquidez y Tesoreria', 'Disponibilidad', 'Rentabilidad', 
    'Eficiencia', 'Endeudamiento'];
  return (
    <>
    <div className={FinancialRatiosStyle.menu_container}>
      {MenuItems.map((item, index) => <button onClick={() => handleMenuClick(index)} 
      className={`${FinancialRatiosStyle.menu_button}
       ${selectedMenu === index ? FinancialRatiosStyle.menu_button_active : ''}`} key={index}>{item}</button>)}
    </div>
    <div className={FinancialRatiosStyle.date_container}><DateComplete/></div>
    <div className={FinancialRatiosStyle.content_container}>
      {selectedMenu === 0 && <Liquidity/>}
      {selectedMenu === 1 && <Availability/>}
      {selectedMenu === 2 && <Rentability/>}
      {selectedMenu === 3 && <Efficiency/>}
      {selectedMenu === 4 && <Indebtedness/>}
    </div>
    </> 
     )
}
