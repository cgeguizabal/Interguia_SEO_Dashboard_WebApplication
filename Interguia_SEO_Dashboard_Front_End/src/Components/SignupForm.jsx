import SignupFormStyle from "../styles/components/SignupForm.module.scss";


function SignupForm() {
  return (
    <form className={SignupFormStyle.container}>
      <div className={SignupFormStyle.inputGroup}>
        {" "}
        <label className={SignupFormStyle.inputGroup_label}>Correo Email</label>{" "}
        <input className={SignupFormStyle.inputGroup_input} type="text" />
      </div>
      <div className={SignupFormStyle.inputGroup}>
        <label className={SignupFormStyle.inputGroup_label}>Contraseña</label>{" "}
        <input className={SignupFormStyle.inputGroup_input} type="password" />
      </div>
      <div className={SignupFormStyle.inputGroup}>
        <label className={SignupFormStyle.inputGroup_label}>Confirmar Contraseña</label>{" "}
        <input className={SignupFormStyle.inputGroup_input} type="password" />
      </div>
      
      <button className={SignupFormStyle.loginButton}>Crear Cuenta</button>
     
    </form>
  );
}

export default SignupForm;