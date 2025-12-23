

import { useState } from "react";
import searchBarStyle from "../styles/components/SearchBar.module.scss";

//Icon
import { FaSearch } from "react-icons/fa";

export default function SearchBar({onSearch}) {

    const [query, setQuery] = useState("");

    const handleChange = (e) =>{
        setQuery(e.target.value);
        onSearch && onSearch(e.target.value);
    }

  return (
    <div className={searchBarStyle.searchContainer}>
        <button className={searchBarStyle.searchButton}><FaSearch /></button>
        <input
     className={searchBarStyle.searchInput} type="text"
      placeholder="Buscar..." value={query} onChange={handleChange} /> 
      </div>
  )
}
