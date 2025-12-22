import AdminLoginForm from "../Components/AdminLoginForm";
import AdminLoginPageStyle from "../styles/pages/AdminLoginPage.module.scss";
import Logo from "../assets/images/Logo_blue.png";


export default function AdminLoginPage() {
  return (
    <div className="grid-container_auto_rows">
        <section className={AdminLoginPageStyle.inputForm}>
              <div className={AdminLoginPageStyle.inputForm_header}>
                      <figure className={AdminLoginPageStyle.inputForm_logo}>
                        <img
                          className={AdminLoginPageStyle.inputForm_logo_image}
                          src={Logo}
                          alt="Logo"
                        />
                      </figure>
                    </div>
            <AdminLoginForm/>
        </section>
    </div>
  )
}
