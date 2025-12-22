import SignupFormStyle from "../styles/components/SignupForm.module.scss";


function SignupForm() {
  return (
    <form className={SignupFormStyle.container}>
      <div className={SignupFormStyle.inputGroup}>
        {" "}
        <h5 className={SignupFormStyle.inputGroup_label}>Correo Email</h5>{" "}
        <input className={SignupFormStyle.inputGroup_input} type="text" />
      </div>
      <div className={SignupFormStyle.inputGroup}>
        <h5 className={SignupFormStyle.inputGroup_label}>Contraseña</h5>{" "}
        <input className={SignupFormStyle.inputGroup_input} type="password" />
      </div>
      <div className={SignupFormStyle.inputGroup}>
        <h5 className={SignupFormStyle.inputGroup_label}>Confirmar Contraseña</h5>{" "}
        <input className={SignupFormStyle.inputGroup_input} type="password" />
      </div>
      
      <button className={SignupFormStyle.loginButton}>Crear Cuenta</button>
     
    </form>
  );
}

export default SignupForm;