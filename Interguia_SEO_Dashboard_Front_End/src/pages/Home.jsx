// import React, { useState } from 'react'
import { useState } from 'react'
import SideMenu from '../Components/SideMenu'
import HomePageStyle from '../styles/pages/Home.module.scss'
import Welcoming from '../Components/Welcoming';
import SearchBar from '../Components/SearchBar';






export default function Home() {

    const [SelectedMenu, SetSelectedMenu] = useState("Presupuestos")
     
    const handleSearch = (value) => {
      console.log("Searching for:", value);
    }
  return (
    <div className={HomePageStyle.container} >      
<SideMenu selected={SelectedMenu} onSelect={SetSelectedMenu} />
<div className={`${HomePageStyle.content} `}>
  <div className={HomePageStyle.navbar_context}><Welcoming /> <SearchBar onSearch={handleSearch} /></div>
  <div className='grid-container_auto_rows_content'>CONTENT</div>

  </div>
   
    </div>
  )
}


// 

