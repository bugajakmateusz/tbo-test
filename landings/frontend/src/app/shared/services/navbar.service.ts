import { Injectable } from '@angular/core';
import {AuthService} from "../../auth/auth.service";

@Injectable({
  providedIn: 'root'
})
export class NavbarService {
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
          name: 'Utwórz raport zakupu',
          path: 'reports/buy',
        },
        {
          name: 'Utwórz raport sprzedaży',
          path: 'reports/sell',
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

  constructor(private authService: AuthService) {}
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
        case 'Utwórz raport zakupu': {
          return true
        }
        case 'Utwórz raport sprzedaźy': {
          return true
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
      }
    }
    if(roles.includes(courierRole)) {
      switch (link) {
        case 'Magazyn': {
          return true
        }
        case 'Włóż towar do maszyny': {
          return true
        }
      }
    }
    return false
  }
}
