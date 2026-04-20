import { useState } from "react";

//const API = import.meta.env.VITE_API_URL;//
const API = "http://185.255.88.111:8000/api/user";


export function useShop() {

    const [loading, setLoading] = useState(false);

    const request = async (
        url: string,
        method: string,
        token: string,
        body?: any
    ) => {

        setLoading(true);

        try {

            const res = await fetch(`${API}${url}`, {
                method,
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`
                },
                body: body ? JSON.stringify(body) : undefined
            });

            const data = await res.json();

            setLoading(false);

            return data;

        } catch (error) {

            setLoading(false);

            return {
                status: "error",
                message: "خطا در اتصال به سرور"
            };
        }
    };

    /* دریافت اطلاعات فروشگاه */
    const getShopData = (token: string) => {
        return request("/shop", "GET", token);
    };

    /* خرید شارژ */
    const buyCharge = (
        token: string,
        data: {
            operator: string
            mobile: string
            amount: number
            payMethod: string
        }
    ) => {
        return request("/shop/charge", "POST", token, data);
    };

    /* خرید اینترنت */
    const buyInternet = (
        token: string,
        data: {
            productId: string
            operator: string
            mobile: string
            internetType: string
            simType: string
            payMethod: string
        }
    ) => {
        return request("/shop/internet", "POST", token, data);
    };

    /* کمک خیریه */
    const payCharity = (
        token: string,
        data: {
            charity: string
            amount: number
            payMethod: string
        }
    ) => {
        return request("/shop/charity", "POST", token, data);
    };

    /* لیست تراکنش ها */
    const getTransactions = (token: string) => {
        return request("/shop/transactions", "GET", token);
    };

    return {
        loading,
        getShopData,
        buyCharge,
        buyInternet,
        payCharity,
        getTransactions
    };
}