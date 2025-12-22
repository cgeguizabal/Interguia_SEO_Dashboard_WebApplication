import { Link } from "react-router-dom";
import LoginFormStyle from "../styles/components/LoginForm.module.scss";


function LoginForm() {
  return (
    <form className={LoginFormStyle.container}>
      <div className={LoginFormStyle.inputGroup}>
        {" "}
        <label className={LoginFormStyle.inputGroup_label}>Correo Email</label>{" "}
        <input className={LoginFormStyle.inputGroup_input} type="text" />
      </div>
      <div className={LoginFormStyle.inputGroup}>
        <label className={LoginFormStyle.inputGroup_label}>Contraseña</label>{" "}
        <input className={LoginFormStyle.inputGroup_input} type="password" />
      </div>
      <label className={LoginFormStyle.keepMeLoggedIn}>
        <input
          className={LoginFormStyle.keepMeLoggedIn_checkbox}
          type="checkbox"
          name="keepMeLoggedIn "
          value="true"
        />
        Mantenme conectado
      </label>
      <button className={LoginFormStyle.loginButton}>Iniciar Sesion</button>
      <Link to="/Signup" className={LoginFormStyle.createAccount}>
        <h3>Crear Cuenta</h3>
      </Link>
    </form>
  );
}

export default LoginForm;
