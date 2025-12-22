import SignupStyle from "../styles/pages/SignupPage.module.scss";
import SignupForm from "../Components/SignupForm";
import Logo from "../assets/images/Logo_blue.png";

import { IoArrowBackCircleSharp } from "react-icons/io5";
import { Link } from "react-router-dom";


function SignupPage() {
  return (
    <div className="grid-container_auto_rows">
      <section className={SignupStyle.coverImage}></section>
      <section className={SignupStyle.inputForm}>
        <div className={SignupStyle.inputForm_header}>
          <figure className={SignupStyle.inputForm_logo}>
            <img
              className={SignupStyle.inputForm_logo_image}
              src={Logo}
              alt="Logo"
            />
          </figure>
        </div>
        <SignupForm />
      </section>
     <Link to="/Login" className={SignupStyle.databaseConfigButton}>
             {" "}
             <IoArrowBackCircleSharp /> {/* Icono de flecha hacia atrás */}
             Regresar a Iniciar Sesión
           </Link>
    </div>
  );
}

export default SignupPage;
