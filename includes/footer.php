    </div>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> 伊豆総株式会社 All Rights Reserved.</p>
    </footer>
    
    <script>
        function toggleMenu() {
            const menu = document.getElementById('mainMenu');
            menu.classList.toggle('active');
        }
        
        // フェードインアニメーション
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in');
            });
        });
    </script>
</body>
</html>
