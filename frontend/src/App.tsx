import {BrowserRouter as Router, Routes, Route} from "react-router-dom";
import {Container} from "@mui/material";

import Login from "./pages/user/Login";
import Verify from "./pages/user/Verify";

import Header from "./components/public/Header";
import Footer from "./components/public/Footer";
import Home from "./pages/Home";
import Collect from "./pages/Collect";
import Messages from "./pages/Messages";
import Profile from "./pages/Profile";
import TicketListPage from "./pages/tickets/TicketList";
import TicketViewPage from "./pages/tickets/TicketView";
import TicketAddPage from "./pages/tickets/TicketAdd";
import Rule from "./pages/Rule";
import Requests from "./pages/Requests";
import Request from "./pages/Request";
import Privacy from "./pages/Privacy";
import WastePrices from "./pages/WastePrices";
import Shop from "./pages/Shop";
import ShopHistory from "./pages/shop/ShopHistory";
import ShopCharity from "./pages/shop/ShopCharity";
import ShopInternet from "./pages/shop/ShopInternet";

function App() {
    return (
        <Router>
            <Routes>

                <Route path="/login" element={<Login/>}/>
                <Route path="/verify" element={<Verify/>}/>

                <Route
                    path="/*"
                    element={
                        <>
                            <Header/>
                            <Container
                                sx={{
                                    height: "100vh",
                                    py: 12,
                                    boxShadow: 3,
                                    overflowY: "scroll",
                                    scrollbarWidth: "none",
                                    "&::-webkit-scrollbar": {display: "none"},
                                }}
                            >
                                <Routes>
                                    <Route path="/" element={<Home/>}/>
                                    <Route path="/collect" element={<Collect/>}/>
                                    <Route path="/messages" element={<Messages/>}/>
                                    <Route path="/profile" element={<Profile/>}/>
                                    <Route path="/requests" element={<Requests/>}/>
                                    <Route path="/request/:id" element={<Request/>}/>
                                    <Route path="/tickets" element={<TicketListPage/>}/>
                                    <Route path="/tickets/new" element={<TicketAddPage/>}/>
                                    <Route path="/tickets/:id" element={<TicketViewPage/>}/>
                                    <Route path="/prices" element={<WastePrices/>}/>
                                    <Route path="/rule" element={<Rule/>}/>
                                    <Route path="/privacy" element={<Privacy/>}/>
                                    <Route path="/shop" element={<Shop/>}/>
                                    <Route path="/shop/history" element={<ShopHistory/>}/>
                                    <Route path="/shop/charity" element={<ShopCharity/>}/>
                                    <Route path="/shop/internet" element={<ShopInternet/>}/>
                                </Routes>
                            </Container>
                            <Footer/>
                        </>
                    }
                />
            </Routes>
        </Router>
    );
}

export default App;