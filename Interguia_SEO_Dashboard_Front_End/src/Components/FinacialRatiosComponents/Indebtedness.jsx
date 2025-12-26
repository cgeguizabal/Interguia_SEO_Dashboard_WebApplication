import IndebtednesStyle from "../../styles/components/Indebtedness.module.scss"
import DateComplete from "../DateComplete"


export default function Indebtedness() {
  return (
    <>    
      <div className={IndebtednesStyle.date_container}><DateComplete/></div>
    </>
  )
}
