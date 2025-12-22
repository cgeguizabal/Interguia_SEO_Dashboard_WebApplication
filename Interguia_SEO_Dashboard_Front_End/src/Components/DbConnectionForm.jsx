import DbConnectionFormStyle from "../styles/components/DbConnectionForm.module.scss";

export default function DbConnectionForm() {
  return (
    <form className={DbConnectionFormStyle.container}>
              <div className={DbConnectionFormStyle.inputGroup}>
                {" "}
                <label className={DbConnectionFormStyle.inputGroup_label}>Nombre de servidor</label>{" "}
                <input className={DbConnectionFormStyle.inputGroup_input} type="text" />
              </div>
              <div className={DbConnectionFormStyle.inputGroup}>
                {" "}
                <label className={DbConnectionFormStyle.inputGroup_label}>Nombre de la base de datos</label>{" "}
                <input className={DbConnectionFormStyle.inputGroup_input} type="text" />
              </div>
              <div className={DbConnectionFormStyle.inputGroup}>
                <label className={DbConnectionFormStyle.inputGroup_label}>Nombre de usuario</label>{" "}
                <input className={DbConnectionFormStyle.inputGroup_input} type="text" />
              </div>
              <div className={DbConnectionFormStyle.inputGroup}>
                <label className={DbConnectionFormStyle.inputGroup_label}>Contraseña</label>{" "}
                <input className={DbConnectionFormStyle.inputGroup_input} type="password" />
              </div>
              

              <button className={DbConnectionFormStyle.loginButton}>Conectar a Base de Datos</button>

            </form>
  )
}
