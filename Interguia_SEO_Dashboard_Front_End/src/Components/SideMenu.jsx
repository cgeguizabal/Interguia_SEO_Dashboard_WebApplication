import SideMenuStyle from '../styles/components/SideMenu.module.scss';
import logo from '../assets/images/Logo_blue.png';

//ICONOS
import { FaMoneyBillTransfer, FaBoxesPacking } from "react-icons/fa6";
import { AiOutlineRise } from "react-icons/ai";
import { FaRegChartBar, FaBoxes } from "react-icons/fa";



export default function SideMenu({ selected, OnSelect}) {



    const menuItems = [
        {icon: <FaMoneyBillTransfer />, label: "Presupuestos"},
        {icon: <AiOutlineRise />, label: "Ratios Financieros"},
        {icon: <FaBoxesPacking />, label: "Distribución"},
        {icon: <FaRegChartBar />, label: "Analisis de Ventas"},
        {icon: <FaBoxes  />, label: "Inventario"},
    ];
  return (
   
        <nav className={SideMenuStyle.menu_container}>
            <div className={SideMenuStyle.menu_wrapper}>
            <figure className={SideMenuStyle.logo_Container}>
                <img className={SideMenuStyle.logo_image} src={logo} alt="" />
            </figure>
            <ul className={SideMenuStyle.menuList}>
                {menuItems.map((item, index) => (
                    <li key={index}><button className={`${SideMenuStyle.menu_button} ${selected === item ? SideMenuStyle.active : ""}`} onClick={() => OnSelect(item)}>{item.icon} {item.label}</button></li>
                ))}
            </ul>
            </div>
            <div>            <button>Salir de sesion</button>
</div>
        </nav>
    
  )
}
