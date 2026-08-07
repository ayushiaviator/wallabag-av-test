<?php

namespace Wallabag\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wallabag\Repository\TagRepository;

class PurgeOrphanTagsCommand extends Command
{
    protected static $defaultName = 'wallabag:purge-orphan-tags';
    protected static $defaultDescription = 'Removes tags which are not attached to any entry';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TagRepository $tagRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command removes every tag left without a single entry attached to it, for instance after a bulk deletion of entries');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tags = $this->tagRepository->findAll();

        $io->text(\sprintf('Checking <info>%d</info> tags', \count($tags)));

        $purged = 0;

        foreach ($tags as $tag) {
            if (0 < \count($tag->getEntries())) {
                continue;
            }

            $io->text(\sprintf('Removing orphan tag <info>%s</info>', $tag->getLabel()));

            $this->entityManager->remove($tag);
            ++$purged;
        }

        $this->entityManager->flush();

        $io->success(\sprintf('Finished purging. %d orphan tags removed.', $purged));

        return 0;
    }
}
