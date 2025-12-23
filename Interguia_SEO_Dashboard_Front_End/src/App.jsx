import { Route, Routes } from "react-router-dom";
import "./App.css";

import LoginPage from "./pages/LoginPage.jsx";
import SignupPage from "./pages/SignupPage.jsx";
import AdminLoginPage from "./pages/AdminLoginPage.jsx";
import DbConnectionPage from "./pages/DbConnectionPage.jsx";
import Home from "./pages/Home.jsx";

function App() {
  return (
    <main>
      <Routes>
        <Route path="/Login" element={<LoginPage />} />
        <Route path="/Signup" element={<SignupPage />} />
        <Route path="/AdminLogin" element={<AdminLoginPage />} />
        <Route path="/DbConnection" element={<DbConnectionPage />} />
        <Route path="/" element={<Home />} />

      </Routes>
    </main>
  );
}

export default App;
