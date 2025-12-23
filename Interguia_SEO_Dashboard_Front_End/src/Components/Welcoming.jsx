import WelcomingStyle from '../styles/components/Welcoming.module.scss';

export default function Welcoming() {
  return (
    <div className={WelcomingStyle.welcoming}>
        <h6 className={WelcomingStyle.welcoming_top}>Hola Carlos Aguilar,</h6>
        <h2 className={WelcomingStyle.welcoming_bottom}>Bievenido a Interguia SEO!</h2>
    </div>
  )
}
