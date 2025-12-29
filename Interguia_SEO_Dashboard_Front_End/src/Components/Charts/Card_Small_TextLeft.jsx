import Card_small_textLeftStyle from "../../styles/components/Card_Small_TextLeft.module.scss"


export default function Card_Small_TextLeft({icon, title, data}) {
  return (
     <>
        <div className={Card_small_textLeftStyle.card_container}>
        
        <div className={Card_small_textLeftStyle.card_text_container}>
          
          <h4 className={Card_small_textLeftStyle.card_text_title}>{title}</h4>
          <p className={Card_small_textLeftStyle.card_text_data}>{data}</p>
          </div>
          <div className={Card_small_textLeftStyle.icon_container}>{icon}</div>
        </div>
        </>
  )
}
