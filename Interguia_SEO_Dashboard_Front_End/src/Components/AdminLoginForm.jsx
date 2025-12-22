import AdminLoginFormStyle from "../styles/components/AdminLoginForm.module.scss";

export default function AdminLoginForm() {
  return (
    <form className={AdminLoginFormStyle.container}>
          <div className={AdminLoginFormStyle.inputGroup}>
            {" "}
            <label className={AdminLoginFormStyle.inputGroup_label}>Correo Email</label>{" "}
            <input className={AdminLoginFormStyle.inputGroup_input} type="text" />
          </div>
          <div className={AdminLoginFormStyle.inputGroup}>
            <label className={AdminLoginFormStyle.inputGroup_label}>Contraseña</label>{" "}
            <input className={AdminLoginFormStyle.inputGroup_input} type="password" />
          </div>
          
          <button className={AdminLoginFormStyle.loginButton}>Iniciar Sesion</button>
         
        </form>
  )
}
