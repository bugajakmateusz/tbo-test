import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class AlertService {
  showAlertForTime(alertId: string, time: number) {
    document.getElementById(alertId)?.classList.remove('d-none');
    setTimeout(() => {
      document.getElementById(alertId)?.classList.add('d-none');
    }, time);
  }
}
