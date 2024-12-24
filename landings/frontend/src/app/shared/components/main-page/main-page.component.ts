import {Component, OnDestroy, OnInit} from '@angular/core';
import {AuthService} from "../../../auth/auth.service";
import {Subject, Subscription} from "rxjs";

@Component({
  selector: 'app-main-page',
  templateUrl: './main-page.component.html',
  styleUrls: ['./main-page.component.scss']
})
export class MainPageComponent implements OnInit {
    isLoggedIn= false
  userFullName = 'nouser'
    userSub = new Subscription()
  constructor(private authService: AuthService) {
  }

  ngOnInit() {
      this.authService.getUserData()
      this.userSub = this.authService.user.subscribe(userData => {
          this.userFullName = `${userData.name} ${userData.surname}`
          this.isLoggedIn = this.authService.isLoggedIn
      })
  }

}
