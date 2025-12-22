// import React, { useState } from 'react'
import { useState } from 'react'
import SideMenu from '../Components/SideMenu'
import HomePageStyle from '../styles/pages/Home.module.scss'



export default function Home() {

    const [SelectedMenu, setSelectedMenu] = useState("Presupuestos")
  return (
    <div className={HomePageStyle.container} >      
<SideMenu selected={SelectedMenu} onSelect={setSelectedMenu} />
<div>CONTENT</div>
    </div>
  )
}


/*

className={`${styles.menuItem} ${
            selected === item ? styles.active : ""
          }`}
          onClick={() => onSelect(item)}
        >
        
*/