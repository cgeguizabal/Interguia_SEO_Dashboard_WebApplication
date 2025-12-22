import LoginStyle from "../styles/pages/LoginPage.module.scss";
import LoginForm from "../Components/LoginForm";
import Logo from "../assets/images/Logo_blue.png";
import { BsDatabaseFillLock } from "react-icons/bs"; //Esto es un icono de react-icons

function LoginPage() {
  return (
    <div className="grid-container_auto_rows">
      <section className={LoginStyle.coverImage}></section>
      <section className={LoginStyle.inputForm}>
        <div className={LoginStyle.inputForm_header}>
          <figure className={LoginStyle.inputForm_logo}>
            <img
              className={LoginStyle.inputForm_logo_image}
              src={Logo}
              alt="Logo"
            />
          </figure>
        </div>
        <LoginForm />
      </section>
      <button className={LoginStyle.databaseConfigButton}>
        {" "}
        <BsDatabaseFillLock /> {/* Icono de candado de base de datos */}
        Configurar conexión
      </button>
    </div>
  );
}

export default LoginPage;
