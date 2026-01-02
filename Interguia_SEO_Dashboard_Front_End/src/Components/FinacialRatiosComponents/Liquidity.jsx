import SpeedGauge from "../Charts/SpeedGauge"
import LiquidityStyle from "../../styles/components/Liquidity.module.scss"


export default function Liquidity() {
  return (
     <>
     <div className={LiquidityStyle.SpeedGauge_container}>
      <SpeedGauge speed={2.6} chart='Razón Corriente' />
      </div>
      </>
  )
}
