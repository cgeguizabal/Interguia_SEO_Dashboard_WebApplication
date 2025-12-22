import { Route, Routes } from "react-router-dom";
import "./App.css";

import LoginPage from "./pages/LoginPage.jsx";
import SignupPage from "./pages/SignupPage.jsx";
import AdminLoginPage from "./pages/AdminLoginPage.jsx";
import DbConnectionPage from "./pages/DbConnectionPage.jsx";

function App() {
  return (
    <main>
      <Routes>
        <Route path="/Login" element={<LoginPage />} />
        <Route path="/Signup" element={<SignupPage />} />
        <Route path="/AdminLogin" element={<AdminLoginPage />} />
        <Route path="/DbConnection" element={<DbConnectionPage />} />

      </Routes>
    </main>
  );
}

export default App;
