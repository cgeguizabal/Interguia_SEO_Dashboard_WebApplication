import { Route, Routes } from "react-router-dom";
import "./App.css";

import LoginPage from "./pages/LoginPage.jsx";
import SignupPage from "./pages/SignupPage.jsx";
import AdminLoginPage from "./pages/AdminLoginPage.jsx";

function App() {
  return (
    <main>
      <Routes>
        <Route path="/Login" element={<LoginPage />} />
        <Route path="/Signup" element={<SignupPage />} />
        <Route path="/AdminLogin" element={<AdminLoginPage />} />
      </Routes>
    </main>
  );
}

export default App;
