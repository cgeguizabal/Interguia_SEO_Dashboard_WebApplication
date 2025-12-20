import { Route, Routes } from "react-router-dom";
import "./App.css";

import LoginPage from "./pages/LoginPage.jsx";

function App() {
  return (
    <main>
      <Routes>
        <Route path="/" element={<LoginPage />} />
      </Routes>
    </main>
  );
}

export default App;
