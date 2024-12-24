import {Component, OnInit} from '@angular/core';
import {Subscription} from "rxjs";
import {AuthService} from "../../../auth/auth.service";
import {Router} from "@angular/router";
import {NavbarService} from "../../services/navbar.service";

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss'],
})
export class NavbarComponent implements OnInit{
  userSub = new Subscription()

  isLoggedIn = false



  links: any[] = []

  constructor(private navbarService: NavbarService, private authService: AuthService, private router: Router) {}
  ngOnInit() {
    this.userSub = this.authService.user.subscribe(userData => {
      this.isLoggedIn = this.authService.isLoggedIn
      this.mapLinks()
    })
  }

  logout() {
    this.authService.logout()
  }

  mapLinks() {
    this.links = []
    this.navbarService.allLinks.forEach(link => {
      let anyArray: any[] = []
      let filteredLink = {name: link.name, icon: link.icon, options: anyArray}
      link.options.forEach(option => {
        if(this.navbarService.showLink(option.name)) {
          filteredLink.options.push(option)
        }
      })
      if(this.navbarService.showLink(link.name)) {
        this.links.push(filteredLink)
      }
    })
  }


}
