        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-2">
                        <i class="fas fa-car me-2"></i>Loja de Carros
                    </h6>
                    <p class="text-muted small mb-0">Sistema completo para gerenciamento automotivo</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted small mb-0">
                        <i class="fas fa-code me-1"></i>
                        Desenvolvido com PHP & Bootstrap
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    
    <!-- Verificar e carregar Font Awesome se necessário -->
    <script>
        (function() {
            function carregarFontAwesomeFallback() {
                // Verificar se Font Awesome carregou testando um ícone
                const testIcon = document.createElement('i');
                testIcon.className = 'fas fa-car';
                testIcon.style.position = 'absolute';
                testIcon.style.left = '-9999px';
                document.body.appendChild(testIcon);
                
                setTimeout(function() {
                    const computed = window.getComputedStyle(testIcon, ':before');
                    const fontFamily = computed.getPropertyValue('font-family');
                    
                    // Se não detectou Font Awesome, carregar fallback
                    if (!fontFamily || !fontFamily.toLowerCase().includes('awesome')) {
                        console.log('Font Awesome não detectado. Carregando fallback...');
                        
                        // Tentar CDN alternativo do jsDelivr
                        const fallbackLink = document.createElement('link');
                        fallbackLink.rel = 'stylesheet';
                        fallbackLink.href = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css';
                        fallbackLink.crossOrigin = 'anonymous';
                        document.head.appendChild(fallbackLink);
                        
                        // Se ainda não funcionar após 1 segundo, tentar outro CDN
                        setTimeout(function() {
                            const testIcon2 = document.createElement('i');
                            testIcon2.className = 'fas fa-car';
                            testIcon2.style.position = 'absolute';
                            testIcon2.style.left = '-9999px';
                            document.body.appendChild(testIcon2);
                            
                            setTimeout(function() {
                                const computed2 = window.getComputedStyle(testIcon2, ':before');
                                const fontFamily2 = computed2.getPropertyValue('font-family');
                                
                                if (!fontFamily2 || !fontFamily2.toLowerCase().includes('awesome')) {
                                    console.log('Tentando último CDN alternativo...');
                                    const finalLink = document.createElement('link');
                                    finalLink.rel = 'stylesheet';
                                    finalLink.href = 'https://use.fontawesome.com/releases/v6.4.0/css/all.css';
                                    document.head.appendChild(finalLink);
                                }
                                
                                document.body.removeChild(testIcon2);
                            }, 500);
                        }, 1000);
                    }
                    
                    document.body.removeChild(testIcon);
                }, 1000);
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', carregarFontAwesomeFallback);
            } else {
                carregarFontAwesomeFallback();
            }
        })();
    </script>
    
    <?php if (file_exists('assets/js/validation.js')): ?>
    <script src="assets/js/validation.js"></script>
    <?php elseif (file_exists('../assets/js/validation.js')): ?>
    <script src="../assets/js/validation.js"></script>
    <?php endif; ?>
</body>
</html>









