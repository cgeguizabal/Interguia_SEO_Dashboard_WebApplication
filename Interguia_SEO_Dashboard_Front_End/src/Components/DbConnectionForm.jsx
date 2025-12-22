import { useState } from "react";
import DbConnectionFormStyle from "../styles/components/DbConnectionForm.module.scss";

export default function DbConnectionForm() {

  const [authMode, setAuthMode] = useState("sql");

  const handleAuthModeChange = (e) => {
    setAuthMode(e.target.value);
  }

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
              <div className={DbConnectionFormStyle.inputGroup}>
                <label className={DbConnectionFormStyle.inputGroup_label}>Autenticación</label>{" "}
                <select className={DbConnectionFormStyle.inputGroup_select} value={authMode} onChange={handleAuthModeChange}>
                  <option value="sql">SQL Server</option>
                  <option value="windows">Windows</option>
                </select>
              </div>
              

              <button className={DbConnectionFormStyle.connectButton}>Conectar a Base de Datos</button>

            </form>
  )
}
