import DateComplete from "../DateComplete"
import InventoryStyle from "../../styles/components/Inventory.module.scss"

export default function Inventory() {
    const ArticleClasses = ["Producto", "Servicio"]

  return (
<>
    <div className={InventoryStyle.date_container}>
      <select className={InventoryStyle.select}>
              {ArticleClasses.map((item, index) => (<option value={item} key={index}>{item}</option>))}
            </select>
      <DateComplete/></div>
    <div>Inventory</div>
    </>
  )
}
