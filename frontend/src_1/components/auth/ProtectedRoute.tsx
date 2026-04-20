// components/auth/ProtectedRoute.tsx
import { Navigate } from "react-router-dom";
import type {ReactNode} from "react";
import {useAuthStore} from "../../store/useAuthStore.ts";

interface ProtectedRouteProps {
    children: ReactNode;
}

const ProtectedRoute = ({ children }: ProtectedRouteProps) => {
    const { accessToken } = useAuthStore();
   // const token = localStorage.getItem("access_token");
    console.log('hahah ',accessToken)

    if (!accessToken) {
        return <Navigate to="/login" replace />;
    }

    return <>{children}</>;
};

export default ProtectedRoute;
