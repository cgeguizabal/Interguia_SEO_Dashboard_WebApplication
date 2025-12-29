import CardLargeStyle from "../../styles/components/CardLarge.module.scss"

export default function Card_Large({title, icon, data, performance}) {
  return (
      <div className={CardLargeStyle.CardLarge_container}>
        <div className={CardLargeStyle.CardLarge_icon}>{icon}</div> 
        <div className={CardLargeStyle.CardLarge_content_container}>
<h3 className={CardLargeStyle.CardLarge_title}>{title}</h3>
        <h1 className={CardLargeStyle.CardLarge_data}>{data}</h1>
        <p className={CardLargeStyle.CardLarge_performance}>{performance}</p>
        </div>
        
    </div>
  )
}
