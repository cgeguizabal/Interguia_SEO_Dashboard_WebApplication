import Card_small_textRightStyle from "../../styles/components/Card_Small_TextRight.module.scss"
export default function Card_Small_TextRight({icon, title, data}) {
  return (
    <>
    <div className={Card_small_textRightStyle.card_container}>
    <div className={Card_small_textRightStyle.icon_container}>{icon}</div>
    <div className={Card_small_textRightStyle.card_text_container}>
      <h4 className={Card_small_textRightStyle.card_text_title}>{title}</h4>
      <p className={Card_small_textRightStyle.card_text_data}>{data}</p></div>
    </div>
    </>
  )
}
