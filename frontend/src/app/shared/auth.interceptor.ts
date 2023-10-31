import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor
} from '@angular/common/http';
import { Observable } from 'rxjs';
import { TokenService } from "../shared/token.service";

@Injectable()
export class AuthInterceptor implements HttpInterceptor {

  constructor(private tokenService: TokenService) { }


  // intercept(request: HttpRequest<unknown>, next: HttpHandler): 
  // Observable<HttpEvent<unknown>> {
  //   return next.handle(request);
  // }
  intercept(req: HttpRequest<any>, next: HttpHandler) {
    const accessToken = this.tokenService.getToken();
    req = req.clone({
      setHeaders: {
        Authorization: "Bearer " + accessToken
      }
    });
    return next.handle(req);
  }
}
