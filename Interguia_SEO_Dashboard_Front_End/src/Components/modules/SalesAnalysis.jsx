import { useState } from "react";
import SalesAnalysisStyle from "../../styles/components/SalesAnalysis.module.scss"
import SectionA from "../SalesAnalysisComponents/SectionA";
import SectionB from "../SalesAnalysisComponents/SectionB";
import SectionC from "../SalesAnalysisComponents/SectionC";

export default function SalesAnalysis() {

  const [selectedMenu, setSelectedMenu] = useState(0);

  const handleMenuClick = (index) =>{
   setSelectedMenu(index);
  }

  const MenuItems = ['Seccion A', "Seccion B", "Seccion C"];

  return (
    <>
    <div className={SalesAnalysisStyle.menu_container}>
      {MenuItems.map((item, index )=> (
        <button onClick={() => handleMenuClick(index)} key={index}
        className={`${SalesAnalysisStyle.menu_button}
               ${selectedMenu === index ? SalesAnalysisStyle.menu_button_active : ''}`}
        >
          {item}
        </button>))
      }
    </div>
   {selectedMenu === 0 && <SectionA/>}
   {selectedMenu === 1 && <SectionB/>}
   {selectedMenu === 2 && <SectionC/>}
    
    </>
    
  )
}
