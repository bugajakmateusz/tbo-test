import {Component, OnInit} from '@angular/core';
import {Subscription} from "rxjs";
import {AuthService} from "../../../auth/auth.service";
import {Router} from "@angular/router";

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss'],
})
export class NavbarComponent implements OnInit{
  userSub = new Subscription()

  isLoggedIn = false

  allLinks = [
    {
      name: 'Magazyn',
      icon: 'archive',
      options: [
        {
          name: 'Przeglądaj magazyn',
          path: 'warehouse/view',
        },
        {
          name: 'Przyjmij dostawę',
          path: 'warehouse/delivery',
        },
        {
          name: 'Włóż towar do maszyny',
          path: 'warehouse/hand-to-courier',
        },
      ],
    },
    {
      name: 'Przekąski',
      icon: 'box-seam',
      options: [
        {
          name: 'Przeglądaj przekąski',
          path: 'snacks/view',
        },
        {
          name: 'Dodaj przekąskę',
          path: 'snacks/add',
        },
      ],
    },
    {
      name: 'Maszyny',
      icon: 'gear',
      options: [
        {
          name: 'Przeglądaj maszyny',
          path: 'machines/view',
        },
        {
          name: 'Dodaj maszynę',
          path: 'machines/add',
        },
      ],
    },
    {
      name: 'Raporty',
      icon: 'graph-up',
      options: [
        {
          name: 'Utwórz raport maszyny/maszyn',
          path: 'reports/machines',
        },
        {
          name: 'Utwórz raport magazynu',
          path: 'reports/warehouse',
        },
        {
          name: 'Utwórz raport zakupu/sprzedaży',
          path: 'reports/buy-sell',
        },
      ],
    },
    {
      name: 'Użytkownicy',
      icon: 'people',
      options: [
        {
          name: 'Przeglądaj użytkowników',
          path: 'users/view',
        },
        {
          name: 'Dodaj użytkownika',
          path: 'users/add',
        },
      ],
    },
  ];

  links: any[] = []

  constructor(private authService: AuthService, private router: Router) {
  }
  ngOnInit() {
    this.userSub = this.authService.user.subscribe(userData => {
      this.isLoggedIn = this.authService.isLoggedIn
      this.allLinks.forEach(link => {
        let anyArray: any[] = []
        let filteredLink = {name: link.name, icon: link.icon, options: anyArray}
        link.options.forEach(option => {
          if(this.showLink(option.name)) {
            filteredLink.options.push(option)
          }
        })
        if(this.showLink(link.name)) {
          this.links.push(filteredLink)
        }
      })
    })
  }

  logout() {
    this.authService.logout()
  }

  showLink(link: string): boolean {
    const roles = this.authService.userRoles
    const adminRole = "ROLE_ADMIN"
    const officeRole = "ROLE_OFFICE_MANAGER"
    const warehouseRole = "ROLE_LOGISTIC_MANAGER"
    const courierRole = "ROLE_COURIER"
    if(roles.includes(adminRole)) {
      return true
    }
    if(roles.includes(officeRole)) {
      switch (link) {
        case 'Magazyn': {
          return true
        }
        case 'Przeglądaj magazyn': {
          return true
        }
        case 'Przekąski': {
          return true
        }
        case 'Przeglądaj przekąski': {
          return true
        }
        case 'Dodaj przekąskę': {
          return true
        }
        case 'Maszyny': {
          return true
        }
        case 'Przeglądaj maszyny': {
          return true
        }
        case 'Dodaj maszynę': {
          return true
        }
        case 'Raporty': {
          return true
        }
        case 'Utwórz raport maszyny/maszyn': {
          return true
        }
        case 'Utwórz raport magazynu': {
          return true
        }
        case 'Utwórz raport zakupu/sprzedaży': {
          return true
        }
        default: {
          return false
        }
      }

    }
    if(roles.includes(warehouseRole)) {
      switch (link) {
        case 'Magazyn': {
          return true
        }
        case 'Przeglądaj magazyn': {
          return true
        }
        case 'Przyjmij dostawę': {
          return true
        }
        case 'Włóż towar do maszyny': {
          return true
        }
        case 'Raporty': {
          return true
        }
        case 'Utwórz raport maszyny/maszyn': {
          return true
        }
        case 'Utwórz raport magazynu': {
          return true
        }
        default: {
          return false
        }
      }

    }
    if(roles.includes(courierRole)) {
      switch (link) {
        case 'Magazyn': {
          return true
        }
        case 'Przeglądaj magazyn': {
          return true
        }
        case 'Włóż towar do maszyny': {
          return true
        }
        default: {
          return false
        }
      }

    }
    return false
  }
}
