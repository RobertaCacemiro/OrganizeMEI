"use client";

import React from "react";
import { useState } from "react";
import Link from "next/link";

import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from "@/components/ui/card";
import { Eye, EyeOff } from "lucide-react"
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export function Register() {
    const [passwordMatch, setPasswordMatch] = useState(true)
    const [password, setPassword] = useState("")
    const [confirmPassword, setConfirmPassword] = useState("")
    const [showPassword, setShowPassword] = useState(false)
    const [showConfirmPassword, setShowConfirmPassword] = useState(false)

    const handlePasswordChange = (e) => {
        setPassword(e.target.value);
        setPasswordMatch(e.target.value === confirmPassword);
    };

    const handleConfirmPasswordChange = (e) => {
        setConfirmPassword(e.target.value);
        setPasswordMatch(e.target.value === password);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (password !== confirmPassword) {
            setPasswordMatch(false);
            return;
        }

        console.log("FFormulário enviado");
    };

    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-[#50d71e] p-4">
            <div className="w-full max-w-md space-y-6">
                <div className="flex justify-center">
                    <img
                        src="/images/logo.png"
                        alt="Logo do Software Organize Mei"
                        className="w-40 h-auto max-w-[300px]"
                    />
                </div>
                <Card className="w-full">
                    <CardHeader className="text-center">
                        <CardTitle className="text-3xl font-bold text-[#50d71e]">Cadastrar-se</CardTitle>
                        <CardDescription>
                            <div className="text-center text-sm">
                                Já possui conta?{" "}
                                <Link href="/" className="underline underline-offset-4 text-[#50d71e] hover:text-[#3c9e15] font-medium">
                                    Acesse agora
                                </Link>
                            </div>
                        </CardDescription>
                    </CardHeader>
                    <form>
                        <CardContent className="space-y-4">
                            <div className="grid gap-6">
                                <div className="grid gap-2">
                                    <Label htmlFor="fullName">Nome Completo:</Label>
                                    <Input id="fullName" placeholder="Digite seu nome completo" required />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="email">E-mail:</Label>
                                    <Input id="email" type="email" placeholder="email@exemplo.com" required />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="password">Senha</Label>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div className="relative">
                                            <Input
                                                id="password"
                                                type={showPassword ? "text" : "password"}
                                                placeholder="Digite sua senha"
                                                value={password}
                                                onChange={handlePasswordChange}
                                                required
                                                className={
                                                    !passwordMatch && password && confirmPassword ? "border-red-500 focus-visible:ring-red-500" : ""
                                                }
                                            />
                                            <button
                                                type="button"
                                                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                                onClick={() => setShowPassword(!showPassword)}
                                            >
                                                {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                                            </button>
                                        </div>
                                        <div className="relative">
                                            <Input
                                                id="confirmPassword"
                                                type={showConfirmPassword ? "text" : "password"}
                                                placeholder="Confirme sua senha"
                                                value={confirmPassword}
                                                onChange={handleConfirmPasswordChange}
                                                required
                                                className={
                                                    !passwordMatch && password && confirmPassword ? "border-red-500 focus-visible:ring-red-500" : ""
                                                }
                                            />
                                            <button
                                                type="button"
                                                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                                onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                            >
                                                {showConfirmPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                                            </button>
                                        </div>
                                    </div>
                                    {/* {!passwordMatch && <p className="text-sm text-red-500">As senhas não coincidem</p>} */}
                                </div>
                                <div className="relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-border">
                                    <span className="relative z-10 bg-background px-2 text-muted-foreground">
                                        Ou continue com
                                    </span>
                                </div>
                                <Button variant="outline" className="w-full cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path
                                            d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"
                                            fill="currentColor"
                                        />
                                    </svg>
                                    Entrar com Google
                                </Button>
                            </div>
                            <Button className="w-full bg-[#50d71e] hover:bg-[#3c9e15] rounded-xl cursor-pointer">Cadastrar-se</Button>
                        </CardContent>
                    </form>
                </Card>
            </div>
        </div>
    );
}
