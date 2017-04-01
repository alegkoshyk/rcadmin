import { Injectable } from '@angular/core';
import {Http} from "@angular/http";

const typePath = 'http:localhost:80/rcadmin/web/index.php/pages/api/types';

@Injectable()
export class AdminService {

  constructor(private http: Http) { }

  public postForm(formData:any) {
    return this.http.post(typePath, formData)
      .map((response) => {
        return response.json();
      })
  }

}
