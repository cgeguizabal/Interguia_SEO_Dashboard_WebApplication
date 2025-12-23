// import React, { useState } from 'react'
import { useState } from 'react'
import SideMenu from '../Components/SideMenu'
import HomePageStyle from '../styles/pages/Home.module.scss'



export default function Home() {

    const [SelectedMenu, SetSelectedMenu] = useState("Presupuestos")
console.log(SelectedMenu);
  return (
    <div className={HomePageStyle.container} >      
<SideMenu selected={SelectedMenu} onSelect={SetSelectedMenu} />
<div className={HomePageStyle.content}>CONTENT</div>
    </div>
  )
}


