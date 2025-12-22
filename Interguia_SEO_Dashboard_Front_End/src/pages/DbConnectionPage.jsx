import DbConnectionPageStyle from "../styles/pages/DbConnectionPage.module.scss";

import Logo from "../assets/images/Logo_blue.png";
import DbConnectionForm from "../Components/DbConnectionForm";

export default function DbConnectionPage() {
  return (
    <div className="grid-container_auto_rows">
      <section className={DbConnectionPageStyle.coverImage}></section>
      <section className={DbConnectionPageStyle.inputForm}>
        <div className={DbConnectionPageStyle.inputForm_header}>
          <figure className={DbConnectionPageStyle.inputForm_logo}>
            <img
              className={DbConnectionPageStyle.inputForm_logo_image}
              src={Logo}
              alt="Logo"
            />
          </figure>
        </div>
        <DbConnectionForm />
      </section>
    </div>
  )
}
